<?php
namespace WhatConverts;


interface WhatConvertsInterface
{

    /*
     * Accounts
     */
    public function getAccounts(array $options);
    public function getAllAccounts(array $options);
    public function getAccount($account_id);
    public function createAccount($account_name, $create_profile);
    public function editAccount($account_id, $account_name); //simply renames account. name is only editable field at this time.
    public function deleteAccount($account_id);

    /*
     * Leads
     */
    public function getLeads(array $options);
    public function getAllLeads(array $options);
    public function getLead($lead_id);
    public function createLead($profile_id, $lead_type, array $attributes);
    public function editLead($lead_id, array $attributes);

    /*
     * Profiles
     */
    public function getProfiles($account_id, array $options);
    public function getAllProfiles($account_id, array $options);
    public function getProfile($account_id, $profile_id);
    public function createProfile($account_id, $profile_name);
    public function editProfile($account_id, $profile_id, $profile_name); //simply renames profile. name is only editable field at this time.
    public function deleteProfile($account_id, $profile_id);

}