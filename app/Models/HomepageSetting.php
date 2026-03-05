<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    use HasFactory;

    protected $attributes = [
        'show_welcome_modal' => true,
        'welcome_modal_heading' => 'Welcome Home, {title} {name}!',
        'welcome_modal_message' => 'We are thrilled to have you lead your congregation in the Centurion Campaign. Together, we will reach our goal of 100 souls per member!',
    ];

    protected $fillable = [
        'hero_heading',
        'hero_description',
        'hero_subtext',
        'background_image',
        'objectives_title',
        'objectives_subtitle',
        'obj_1_title',
        'obj_1_description',
        'obj_2_title',
        'obj_2_description',
        'obj_3_title',
        'obj_3_description',
        'welcome_modal_message',
        'show_welcome_modal',
        'welcome_modal_heading',
    ];
}
