<?php

/*
 * Provider catalogue for the Company Settings UI. Add a provider adapter in
 * app/Services/LeadProviders before setting `enabled` to true.
 */
return [
    'meta_lead_ads' => [
        'name' => 'Meta Lead Ads', 'description' => 'Facebook & Instagram instant forms', 'enabled' => true, 'mode' => 'webhook', 'icon' => 'facebook',
        'requirements' => ['Page ID', 'Long-lived Page access token', 'App secret', 'Webhook verify token', 'Form ID (optional)'],
    ],
    '99acres' => [
        'name' => '99acres', 'description' => 'Property enquiry API', 'enabled' => true, 'mode' => 'polling', 'icon' => 'building-2',
        'requirements' => ['API endpoint URL', 'Username', 'Password'],
    ],
    'website_api' => [
        'name' => 'Website form / API', 'description' => 'Receive leads from your website in real time', 'enabled' => true, 'mode' => 'webhook', 'icon' => 'globe-2',
        'requirements' => ['Webhook signing secret (optional)', 'Field mapping'],
    ],
    'generic_webhook' => [
        'name' => 'Generic webhook', 'description' => 'Any tool that can send a JSON webhook', 'enabled' => true, 'mode' => 'webhook', 'icon' => 'webhook',
        'requirements' => ['Webhook signing secret (optional)', 'Field mapping'],
    ],
    'google_forms' => [
        'name' => 'Google Forms', 'description' => 'Receive each Google Form response in real time', 'enabled' => true, 'mode' => 'webhook', 'icon' => 'clipboard-pen-line',
        'requirements' => ['Installable Google Apps Script trigger', 'Unique webhook URL', 'Field mapping'],
    ],
    'google_lead_forms' => ['name' => 'Google Ads Lead Forms', 'description' => 'Google Ads lead-form asset API', 'enabled' => false, 'mode' => 'planned', 'icon' => 'search', 'requirements' => ['Google Ads customer ID', 'Developer token', 'OAuth client credentials']],
    'magicbricks' => ['name' => 'Magicbricks', 'description' => 'Property portal API', 'enabled' => false, 'mode' => 'planned', 'icon' => 'buildings', 'requirements' => ['Account ID', 'API key', 'API secret']],
    'housing' => ['name' => 'Housing.com', 'description' => 'Property portal API', 'enabled' => false, 'mode' => 'planned', 'icon' => 'house', 'requirements' => ['Account ID', 'API key', 'API secret']],
    'indiamart' => ['name' => 'IndiaMART', 'description' => 'Marketplace lead API', 'enabled' => false, 'mode' => 'planned', 'icon' => 'store', 'requirements' => ['CRM key', 'API endpoint URL']],
];
