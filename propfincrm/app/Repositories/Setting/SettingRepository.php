<?php
namespace App\Repositories\Setting;

use App\Models\Setting;

/**
 * Class SettingRepository
 * @package App\Repositories\Setting
 */
class SettingRepository implements SettingRepositoryContract
{
    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->getSetting()->company;
    }

    /**
     * @param $requestData
     */
    public function updateOverall($requestData)
    {
        $setting = $this->getSetting();

        $setting->fill($requestData->all())->save();
    }

    /**
     * @return mixed
     */
    public function getSetting()
    {
        $setting = Setting::query()->first();

        if (!$setting) {
            $company = app(\App\Support\CurrentCompany::class)->get();
            $setting = Setting::create([
                'company_id' => $company?->id,
                'company' => $company?->name ?? config('app.name', 'ElyLeads'),
                'task_complete_allowed' => 2,
                'task_assign_allowed' => 2,
                'lead_complete_allowed' => 2,
                'lead_assign_allowed' => 2,
                'time_change_allowed' => 2,
                'comment_allowed' => 2,
                'timezone' => 'Asia/Kolkata',
            ]);
        }

        return $setting;
    }
}
