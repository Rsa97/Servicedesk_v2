<?php
namespace Backend\API;

class Contact
{
    public static function getAllowed(array $params, array $payload) : array
    {
        $all = $params['all'] ?? false;
        $divisionId = $params['division'] ?? null;
        $user = \Backend\API\User::getById($payload['user']);
        if (!in_array($divisionId, $user->getAllowedDivisionIds(!$all))) {
            return [];
        }
        $contactIds = [];
        $division = \Backend\API\Division::getById($divisionId);
        if (in_array(
            'change_contact',
            \Backend\Config\User::ALLOWED_ACTIONS[$user->rights][\Backend\API\RequestState::RECEIVED]
        )) {
            $contactIds = array_merge(
                $contactIds,
                array_map(
                    function ($contact) {
                        return $contact->id;
                    },
                    $division->users
                ),
                array_map(
                    function ($contact) {
                        return $contact->id;
                    },
                    $division->contract->users
                )
            );
        }
        return array_map(
            function ($contact) use ($user) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->fullName,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'default' => $contact->id === $user->id
                ];
            },
            \Backend\API\User::getListByIds(array_unique($contactIds))
        );
    }
}
