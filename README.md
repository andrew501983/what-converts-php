# WhatConverts PHP 

A PHP interface for the WhatConverts API at https://www.whatconverts.com/api

## Installation via Composer

Install the latest version using Composer.
composer require "andrew501983/what-converts-php"
or add to your composer.json and perform "composer install"

## Usage

Please peruse the WhatConverts API documentation at https://www.whatconverts.com/api
Functionality is split into 3 resources: Accounts, Leads, and Profiles

WhatConverts PHP implements a WhatConvertsInterface which allows for easy documentation.
	
	//Accounts Resource
    public function getAccounts(array $options);
    public function getAllAccounts(array $options);
    public function getAccount($account_id);
    public function createAccount($account_name, $create_profile);
    public function editAccount($account_id, $account_name);
    public function deleteAccount($account_id);
    //Leads Resource 
    public function getLeads(array $options);
    public function getAllLeads(array $options);
    public function getLead($lead_id);
    public function createLead($profile_id, $lead_type, array $attributes);
    public function editLead($lead_id, array $attributes);
    //Profiles Resource
    public function getProfiles($account_id, array $options);
    public function getAllProfiles($account_id, array $options);
    public function getProfile($account_id, $profile_id);
    public function createProfile($account_id, $profile_name);
    public function editProfile($account_id, $profile_id, $profile_name);
    public function deleteProfile($account_id, $profile_id);

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/andrew501983/what-converts-php

## License

The package is available as open source under the terms of the [MIT License](http://opensource.org/licenses/MIT).