<?php

namespace App\Http\Controllers\Settings;

use App\ContactField;
use App\ContactFieldType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ContactFieldTypeRequest;

class PersonalizationController extends Controller
{
    /**
     * Display the personalization page
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings.personalization.index');
    }

    /**
     * Get all the contact field types
     */
    public function getContactFieldTypes()
    {
        return auth()->user()->account->contactFieldTypes;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContactFieldTypeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeContactFieldType(ContactFieldTypeRequest $request)
    {
        $contactFieldType = auth()->user()->account->contactFieldTypes()->create(
            $request->only([
                'name',
                'protocol',
            ])
            + [
                'fontawesome_icon' => $request->get('icon'),
                'account_id' => auth()->user()->account->id,
            ]
        );

        return $contactFieldType;
    }

    /**
     * Edit a newly created resource in storage.
     *
     * @param ContactFieldTypeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function editContactFieldType(ContactFieldTypeRequest $request)
    {
        $contactFieldType = ContactFieldType::findOrFail($request->get('id'));

        $request->user()->update(
            $request->only([
                'email',
                'timezone',
                'locale',
                'currency_id',
                'name_order',
            ]) + [
                'fluid_container' => $request->get('layout'),
            ]
        );

        $contactFieldType->update(
            $request->only([
                'name',
                'protocol',
            ])
            + [
                'fontawesome_icon' => $request->get('icon'),
            ]
        );

        return $contactFieldType;
    }

    public function destroyContactFieldType(ContactFieldType $contactFieldType)
    {
        // find all the contact fields that have this contact field types
        $contactFields = auth()->user()->account->contactFields
                                ->where('contact_field_type_id', $contactFieldType->id);

        foreach ($contactFields as $contactField) {
            $contactField->delete();
        }

        $contactFieldType->delete();
    }
}
