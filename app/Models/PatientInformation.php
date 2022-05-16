<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'special_information',
        'institute_name',
        'institute_contact_number',
        'institute_contact_person',
        'institute_full_address',
        'institute_week_days',
        'classes_from',
        'classes_to',
        'company_name',
        'company_contact_person',
        'company_contact_number',
        'company_full_address',
        'company_week_days',
        'from_timing',
        'to_timing',
        'aids',
        'another_activity',
        'another_activity_name',
        'another_activity_contact_person',
        'activitys_contact_number',
        'activitys_full_address',
        'another_activity_start_time',
        'another_activity_end_time',
        'week_days',
        'issuer_name',
        'number_of_hours',
        'period',
    ];
}
