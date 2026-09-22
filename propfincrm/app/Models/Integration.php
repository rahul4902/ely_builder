<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Dinero;

class Integration extends Model
{
    use \App\Models\Concerns\BelongsToCompany;

    protected $fillable = ['name', 'client_id', 'client_secret', 'api_key', 'org_id', 'api_type', 'company_id'];

    /**
     * @param $type
     * @return mixed
     * @throws \Exception
     */
    public static function getApi($type)
    {
        try {
            $integration = Integration::where([
                //'user_id' => $userId,
                'api_type' => $type
            ])->get();

            if ($integration) {
                $apiConfig = $integration[0];

                $className = $apiConfig->name;

                call_user_func_array(['App\\' . $className, 'initialize'], [$apiConfig]);
                $apiInstance = call_user_func_array(['App\\Models\\' . $className, 'getInstance'], []);

                return $apiInstance;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
