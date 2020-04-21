<?php
namespace Backend\ORM;

class Entity
{
    protected static string $table = '';
    protected static string $desc = 'Базовый класс ORM';
    protected static array $map = [];
    protected static array $cache = [];

    protected static function isType(string $type, $value) : bool
    {
        if (preg_match('/(\S+)\s+of\s+(.+)/', $type, $matches)) {
            $baseType = $matches[1];
            $childType = $matches[2];
        } else {
            $baseType = $type;
            $childType = null;
        }
        if ('?' === substr($baseType, 0, 1)) {
            if (null === $value) {
                return true;
            }
            $baseType = substr($baseType, 1);
        }
        switch ($baseType) {
            case 'numeric':
                return is_numeric($value);
            case 'string':
                return is_string($value);
            case 'boolean':
                return is_bool($value);
            case 'uuid':
                return preg_match('/^[0-9a-f]{32}$/i', $value);
            case 'datetime':
                return preg_match('/^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$/', $value);
            case 'date':
                return preg_match('/^\d\d\d\d-\d\d-\d\d$/', $value);
            case 'time':
                return preg_match('/^\d\d:\d\d:\d\d$/', $value);
            case 'ref':
                return is_object($value);
            case 'backRef':
            case 'refm2m':
                return is_array($value);
            case 'level':
                return in_array($value, SlaLevel::LIST);
            case 'dayType':
                return is_numeric($value) && (($value & DayType::WORKDAY) ||  ($value & DayType::WEEKDAY));
            case 'event':
                return in_array($value, EventType::LIST);
            case 'state':
                return in_array($value, RequestState::LIST);
            case 'sendMethod':
                return in_array($value, SendMethod::LIST);
            case 'rights':
                return in_array($value, UserRights::LIST);
            case 'array':
                if (!is_array($value)) {
                    return false;
                }
                if (null !== $childType) {
                    foreach ($value as $el) {
                        if (!static::isType($childType, $el)) {
                            return false;
                        }
                    }
                }
                return true;
            default:
                return false;
        }
    }

    protected static function typeFromDB(string $type, $value)
    {
        if ('?' === substr($type, 0, 1)) {
            if (null === $value) {
                return null;
            }
            $type = substr($type, 1);
        }
        switch ($type) {
            case 'numeric':
                return floatval($value);
            case 'string':
                return "{$value}";
            case 'boolean':
                return ('1' === $value) || (1 === $value) || (true === $value);
            case 'uuid':
                return unpack('H32', $value)[1];
            case 'dayType':
                $result = 0;
                $types = explode(',', $value);
                foreach (DayType::LIST as $code => $name) {
                    if (in_array($name, $types)) {
                        $result |= $code;
                    }
                }
                return $result;
            default:
                return $value;
        }
    }

    protected static function typeToDB(string $type, $value)
    {
        if ('?' === substr($type, 0, 1)) {
            if (null === $value) {
                return null;
            }
            $type = substr($type, 1);
        }
        switch ($type) {
            case 'numeric':
                return floatval($value);
            case 'string':
                return "{$value}";
            case 'boolean':
                return $value ? 1 : 0;
            case 'uuid':
                return pack('H32', $value);
            case 'dayType':
                $result = [];
                foreach (DayType::LIST as $code => $name) {
                    if ($value & $code) {
                        $result[] = $name;
                    }
                }
                return implode(',', $result);
            default:
                return $value;
        }
    }

    public function __construct(array $inValues, $fromDB = false)
    {
        if ($fromDB) {
            foreach (static::$map as $name => $fieldDef) {
                if (array_key_exists('field', $fieldDef) && array_key_exists($fieldDef['field'], $inValues)) {
                    $this->{$name} = static::typeFromDB($fieldDef['type'], $inValues[$fieldDef['field']]);
                }
            }
        } else {
            foreach ($inValues as $name => $value) {
                $this->{$name} = $value;
            }
        }
        $settedVars = get_object_vars($this);
        foreach (static::$map as $name => $fieldDef) {
            if (($fieldDef['required'] ?? false) && !array_key_exists($name, $settedVars)) {
                if (array_key_exists('default', $fieldDef)) {
                    $this->{$name} = $fieldDef['default'];
                } elseif ('?' === substr($fieldDef['type'], 0, 1)) {
                    $this->{$name} = null;
                } else {
                    throw new \Exception("Required value {$name} is not set");
                }
            }
        }
    }

    protected static function buildCondition(array $definition, \Backend\Common\Counter $counter) : array
    {
        if (!array_key_exists($definition[0], static::$map)) {
            throw new \Exception("Field {$definition[0]} is not exists");
        }
        $fieldDef = static::$map[$definition[0]];
        switch ($definition[1]) {
            case 'IS NULL':
            case 'IS NOT NULL':
                return [
                    'conditions' => "(`{$fieldDef['field']}` {$definition[1]})",
                    'values' => []
                ];
            case '>':
            case '<':
            case '=':
            case '<>':
            case '>=':
            case '<=':
                if (!static::isType($fieldDef['type'], $definition[2])) {
                    throw new \Exception("Incorrect value for field {$definition[0]}");
                }
                $ctr = $counter->next();
                return [
                    'conditions' => "(`{$fieldDef['field']}` {$definition[1]} :value{$ctr})",
                    'values' => ["value{$ctr}" => static::typeToDb($fieldDef['type'], $definition[2])]
                ];
            case 'BETWEEN':
                if (!static::isType($fieldDef['type'], $definition[2])
                    || !static::isType($fieldDef['type'], $definition[3])) {
                    throw new \Exception("Incorrect value for field {$definition[0]}");
                }
                $ctr1 = $counter->next();
                $ctr2 = $counter->next();
                return [
                    'conditions' => "(`{$fieldDef['field']}` {$definition[1]} :value{$ctr1} AND :value{$ctr2})",
                    'values' => [
                        "value{$ctr1}" => static::typeToDb($fieldDef['type'], $definition[2]),
                        "value{$ctr2}" => static::typeToDb($fieldDef['type'], $definition[3])
                    ]
                ];
            case 'IN':
            case 'NOT IN':
                $values = [];
                $condition = [];
                foreach ($definition[2] as $val) {
                    if (!static::isType($fieldDef['type'], $val)) {
                        throw new \Exception("Incorrect value for field {$definition[0]}");
                    }
                    $ctr = $counter->next();
                    $condition[] = ":value{$ctr}";
                    $values[":value{$ctr}"] = static::typeToDb($fieldDef['type'], $val);
                }
                return [
                    'conditions' => "`{$fieldDef['field']}` IN (" . implode(',', $condition) . ")",
                    'values' => $values
                ];
            default:
                throw new \Exception("Invalid condition {$definition[1]}");
        }
    }

    protected static function buildConditionList(array $definitions, \Backend\Common\Counter $counter) : array
    {
        $result = [
            'conditions' => [],
            'values' => []
        ];
        foreach ($definitions as $definition) {
            $conditions = static::buildConditions($definition, $counter);
            $result['conditions'][] = $conditions['conditions'];
            $result['values'][] = $conditions['values'];
        }
        return $result;
    }


    protected static function buildConditions(
        array $definitions,
        \Backend\Common\Counter $counter = null
    ) : array {
        if (null == $counter) {
            $counter =  new \Backend\Common\Counter();
        }
        switch (true) {
            case 'NOT' == $definitions[0]:
                $conditions = static::buildConditionList($definitions[1], $counter);
                return [
                    'conditions' => 'NOT (' . $conditions['conditions'] . ')',
                    'values' => $conditions['values']
                ];
            case 'OR' == $definitions[0]:
            case 'AND' == $definitions[0]:
                $conditions = static::buildConditionList($definitions[1], $counter);
                return [
                    'conditions' => '(' . implode(' ' . $definitions[0] . ' ', $conditions['conditions']) . ')',
                    'values' => array_reduce($conditions['values'], 'array_merge', [])
                ];
            case is_array($definitions[0]):
                $conditions = static::buildConditionList($definitions, $counter);
                return [
                    'conditions' => '(' . implode(' AND ', $conditions['conditions']) . ')',
                    'values' => array_reduce($conditions['values'], 'array_merge', [])
                ];
            default:
                return static::buildCondition($definitions, $counter);
        }
    }

    protected static function dbFieldsList() : string
    {
        $list = [];
        foreach (static::$map as $fieldDef) {
            if (array_key_exists('field', $fieldDef)) {
                $list[] = $fieldDef['field'];
            }
        }
        return '`' . implode('`,`', $list) . '`';
    }

    protected function dbValuesList() : array
    {
        $list = [];
        foreach (static::$map as $name => $fieldDef) {
            if (array_key_exists('field', $fieldDef)) {
                $list[$fieldDef['field']] = static::typeToDB($fieldDef['type'], $this->{$name});
            }
        }
        return $list;
    }

    protected static function getListByFilterFromDB(array $filter) : array
    {
        $fields = static::dbFieldsList();
        $conditions = static::buildConditions($filter);
        $sql = "SELECT {$fields} FROM `" . static::$table . "` WHERE {$conditions['conditions']}";
        $db = DB::get();
        $req = $db->prepare($sql);
        $req->execute($conditions['values']);
        $result = [];
        while ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            $entity = new static($row, true);
            $result[] = $entity;
        }
        return $result;
    }

    public function getIdsListByFilter(array $filter) : array
    {
        $conditions = static::buildConditions($filter);
        $idField = static::$map['id']['field'];
        $sql = "SELECT `{$idField}` AS `id` FROM `" . static::$table . "` WHERE {$conditions['conditions']}";
        $db = DB::get();
        $req = $db->prepare($sql);
        $req->execute($conditions['values']);
        $result = [];
        while ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = static::typeFromDB(static::$map['id']['type'], $row['id']);
        }
        return $result;
    }

    public static function getById($id)
    {
        if (!array_key_exists($id, static::$cache)) {
            $result = static::getListByFilter(['id', '=', $id]);
            if (count($result) == 0) {
                return null;
            }
            static::$cache[$id] = $result[0];
        }
        return static::$cache[$id];
    }

    public static function getListByFilter(array $filter) : array
    {
        if (!array_key_exists('id', static::$map)) {
            return static::getListByFilterFromDB($filter);
        }
        $ids = static::getIdsListByFilter($filter);
        $result = [];
        foreach ($ids as $id) {
            $result[] = static::getById($id);
        }
        return $result;
    }

    protected function insert()
    {
        $values = $this->dbValuesList();
        $fields = '`' . implode('`, `', array_keys($values)) . '`';
        $names = array_map(
            function ($el) {
                return ":{$el}";
            },
            array_keys($values)
        );
        $sql = "INSERT INTO `" . static::$table . "` ({$fields}) VALUES (" . implode(',', $names). ")";
        $db = DB::get();
        $req = $db->prepare($sql);
        $req->execute($values);
        if (array_key_exists('id', static::$map)) {
            $this->id = $db->lastInsertId();
        }
    }

    protected function update()
    {
        $values = $this->dbValuesList();
        $idField = static::$map['id']['field'];
        $update = [];
        foreach (array_keys($values) as $field) {
            if ($field != $idField) {
                $update[] = "`{$field}` = :{$field}";
            }
        }
        $sql = "UPDATE `" . static::$table . "` SET " . implode(',', $update). " WHERE {$idField} = :{$idField}";
        $db = DB::get();
        $req = $db->prepare($sql);
        $req->execute($values);
    }

    public function store()
    {
        if (!array_key_exists('id', static::$map)) {
            throw new \Exception("Can't store record without id field");
        } elseif (null === $this->id) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    public function __get(string $name)
    {
        if (array_key_exists($name, static::$map)) {
            $fieldDef = static::$map[$name];
            switch ($fieldDef['type']) {
                case 'ref':
                case '?ref':
                    if (null === ($this->{$name} ?? null)) {
                        $refField = $fieldDef['refField'];
                        $class = $fieldDef['class'];
                        if (null !== ($this->{$refField} ?? null)) {
                            $this->{$name} = $class::getById($this->{$refField});
                        }
                    }
                    return $this->{$name} ?? null;
                case 'backRef':
                case '?backRef':
                    if (null === ($this->{$name} ?? null)) {
                        $this->{$name} = $fieldDef['class']::getListByFilter([$fieldDef['refField'], '=', $this->id]);
                    }
                    return $this->{$name} ?? null;
                case 'refm2m':
                case '?refm2m':
                    if (null === ($this->{$name} ?? null)) {
                        $links = $fieldDef['linkClass']::getListByFilter([$fieldDef['backRefField'], '=', $this->id]);
                        $ids = [];
                        foreach ($links as $link) {
                            $ids[] = $link->{$fieldDef['refField']};
                        }
                        $this->{$name} = $fieldDef['class']::getListByFilter(['id', 'IN', $ids]);
                    }
                    return $this->{$name} ?? null;
                default:
                    return $this->{$name} ?? null;
            }
        }
        return null;
    }

    public function __set(string $name, $value)
    {
        if (array_key_exists($name, static::$map)) {
            if ((static::$map[$name]['readonly'] ?? false) && array_key_exists($name, get_object_vars($this))) {
                throw new \Exception("Readonly value {$name}");
            }
            if (!static::isType(static::$map[$name]['type'], $value)) {
                throw new \Exception("Incorrect value {$name}");
            }
            switch (static::$map[$name]['type']) {
                default:
                    $this->{$name} = $value;
            }
        }
    }

    public function __isset(string $name)
    {
        return isset($this->{$name});
    }

    public function __unset(string $name)
    {
        if (static::$map[$name]['readonly'] ?? false) {
            throw new \Exception("Readonly value {$name}");
        }
        unset($this->$name);
    }

    public function getDescription() : array
    {
        $result[] = [
            'desc' => static::$desc,
            'fields' => []
        ];
        foreach (static::$map as $name => $def) {
            $nullable = false;
            $type = $def['type'];
            if ('?' == substr($type, 0, 1)) {
                $nullable = true;
                $type = substr($type, 1);
            }
            $link = null;
            $class = null;
            switch ($type) {
                case 'ref':
                case 'backRef':
                    $class = explode('\\', $def['class']);
                    $class = array_pop($class);
                    break;
                case 'refm2m':
                    $link = explode('\\', $def['linkClass']);
                    $link = array_pop($link);
                    $class = explode('\\', $def['class']);
                    $class = array_pop($class);
                    break;
            }
            $virtual = !array_key_exists('field', $def);
            $result['fields'][] = [
                'name' => $name,
                'type' => $type,
                'nullable' => $nullable,
                'virtual' => $virtual,
                'readonly' => $def['readonly'] ?? false,
                'desc' => $def['desc'] ?? '',
                'link' => $link,
                'class' => $class
            ];
        }
        return $result;
    }
}
