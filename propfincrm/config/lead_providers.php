<?php

/*
 * Provider catalogue for the Company Settings UI. Add a provider adapter in
 * app/Services/LeadProviders before setting `enabled` to true.
 */
return [
    '99acres' => ['name' => '99acres', 'description' => 'Property enquiry API', 'enabled' => true, 'icon' => 'building-2', 'requirements' => ['API endpoint URL', 'Username', 'Password']],
    'meta_lead_ads' => ['name' => 'Meta Lead Ads', 'description' => 'Facebook & Instagram forms', 'enabled' => false, 'icon' => 'facebook', 'requirements' => ['Meta App ID', 'Long-lived access token', 'Page ID', 'Form ID', 'Webhook verify token']],
    'google_lead_forms' => ['name' => 'Google Lead Forms', 'description' => 'Google Ads lead forms', 'enabled' => false, 'icon' => 'search', 'requirements' => ['Google Ads customer ID', 'Developer token', 'OAuth client ID', 'OAuth client secret', 'Refresh token']],
    'magicbricks' => ['name' => 'Magicbricks', 'description' => 'Property portal API', 'enabled' => false, 'icon' => 'buildings', 'requirements' => ['Account ID', 'API key', 'API secret']],
    'housing' => ['name' => 'Housing.com', 'description' => 'Property portal API', 'enabled' => false, 'icon' => 'house', 'requirements' => ['Account ID', 'API key', 'API secret']],
    'indiamart' => ['name' => 'IndiaMART', 'description' => 'Marketplace lead API', 'enabled' => false, 'icon' => 'store', 'requirements' => ['CRM key', 'API endpoint URL']],
    'website_api' => ['name' => 'Website API', 'description' => 'Your website lead endpoint', 'enabled' => false, 'icon' => 'globe-2', 'requirements' => ['Endpoint URL', 'API key or bearer token', 'Webhook secret']],
    'generic_webhook' => ['name' => 'Generic Webhook', 'description' => 'Custom JSON webhook', 'enabled' => false, 'icon' => 'webhook', 'requirements' => ['Webhook URL', 'Signing secret', 'Expected JSON field mapping']],
];
