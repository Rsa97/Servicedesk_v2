<?php
namespace Backend\API;

class Calendar
{
    public static function calcTime(
        string $divisionId,
        string $serviceId,
        string $level,
        int $createdAt = null
    ) : array {
        $reactBefore = null;
        $fixBefore = null;
        $repairBefore = null;
        if ($createdAt === null) {
            $createdAt = time();
        }
        $division = \Backend\API\Division::getById($divisionId);
        $sql =
            "SELECT `c`.`date`, `s`.`startDayTime` AS `start`, `s`.`endDayTime` AS `end`, "
            .      "`s`.`toReact`, `s`.`toFix`, `s`.`toRepair` "
            .   "FROM `divServicesSLA` AS `s` "
            .   "JOIN `workCalendar` AS `c` "
            .       "ON `s`.`contract_guid` = :contractId "
            .           "AND `s`.`divType_guid` = :divisionTypeId "
            .           "AND `s`.`service_guid` = :serviceId "
            .           "AND `s`.`slaLevel` = :levelId "
            .           "AND FIND_IN_SET(`c`.`type`, `s`.`dayType`) "
            .   "WHERE `c`.`date` >= :startDate "
            .   "ORDER BY `c`.`date` "
            .   "LIMIT 60";
        $db = \Backend\Common\DB::get();
        $req = $db->prepare($sql);
        $startDate = strftime('%Y-%m-%d', $createdAt);
        $startTime = strftime('%H:%M:00', $createdAt);
        $toReact = 0;
        $toFix = 0;
        $toRepair = 0;
        $reactBefore = null;
        $fixBefore = null;
        $repairBefore = null;
        $req->execute([
            'contractId' => pack('H32', $division->contractId),
            'divisionTypeId' => pack('H32', $division->typeId),
            'serviceId' => pack('H32', $serviceId),
            'levelId' => $level,
            'startDate' => $startDate
        ]);
        while ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            if ($row['end'] === '00:00:00') {
                $row['end'] = '24:00:00';
            }
            if ($startDate === $row['date']) {
                $toReact = $row['toReact'] * 60;
                $toFix = $row['toFix'] * 60;
                $toRepair = $row['toRepair'] * 60;
                if ($startTime >= $row['end']) {
                    continue;
                }
                if ($startTime > $row['start']) {
                    $row['start'] = $startTime;
                }
            }
            $delta = strtotime($row['end']) - strtotime($row['start']);
            if ($reactBefore === null) {
                if ($toReact > $delta) {
                    $toReact -= $delta;
                } else {
                    $reactBefore = strtotime("{$row['date']} {$row['start']}") + $toReact;
                }
            }
            if ($fixBefore === null) {
                if ($toFix > $delta) {
                    $toFix -= $delta;
                } else {
                    $fixBefore = strtotime("{$row['date']} {$row['start']}") + $toFix;
                }
            }
            if ($repairBefore === null) {
                if ($toRepair > $delta) {
                    $toRepair -= $delta;
                } else {
                    $repairBefore = strtotime("{$row['date']} {$row['start']}") + $toRepair;
                }
            }
            if ($reactBefore !== null && $fixBefore !== null && $repairBefore !== null) {
                break;
            }
        }
        return [
            'createdAt' => $createdAt,
            'reactBefore' => $reactBefore,
            'fixBefore' => $fixBefore,
            'repairBefore' => $repairBefore
        ];
    }

    public static function calcInterval(
        string $divisionId,
        string $serviceId,
        string $level,
        int $startTime,
        ?int $endTime
    ) : int {
        if ($endTime === null) {
            $endTime = time();
        }
        $sql = "SELECT calctime_v5(:division, :service, :level, :start, :end) AS `interval`";
        $db = \Backend\Common\DB::get();
        $req = $db->prepare($sql);
        $req->execute([
            'division' => pack('H32', $divisionId),
            'service' => pack('H32', $serviceId),
            'level' => $level,
            'start' => strftime('%Y-%m-%d %H:%M:00', $startTime),
            'end' => strftime('%Y-%m-%d %H:%M:00', $endTime)
        ]);
        if ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            return intval($row['interval']);
        }
        return 0;
    }
}
