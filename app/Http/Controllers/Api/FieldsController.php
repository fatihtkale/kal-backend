<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fields;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FieldsController extends Controller
{
    public function index(Request $request) {
        $fields = Fields::where('company_id', $request->get_company_id)->whereNull('is_deleted')->get();

        return response()->json([
            'fields' => $fields
        ]);
    }

    public function single(Request $request) {
        $field = Fields::where('company_id', $request->get_company_id)->where('id', $request->id)->whereNull('is_deleted')->first();

        if ($field) {
            return response()->json($field);
        } else {
            return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function create(Request $request) {
        if ($request->primary) {
            Fields::where('company_id', $request->get_company_id)->update([
                'is_primary' => null
            ]);
        }

        $field = Fields::create([
            'company_id' => $request->get_company_id,
            'title' => ucfirst($request->title),
            'fields' => $request->fields,
            'is_primary' => $request->primary
        ]);

        if ($field) {
            return response()->json([
                'message' => 'Field created successfully',
                'field' => $field
            ], Response::HTTP_CREATED);
        } else {
            return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request) {
        if ($request->primary) {
            Fields::where('company_id', $request->get_company_id)->update([
                'is_primary' => null
            ]);
        }

        $field = Fields::where('id', $request->id)->where('company_id', $request->get_company_id)->update([
            'title' => ucfirst($request->title),
            'fields' => $request->fields,
            'is_primary' => $request->primary
        ]);

        if ($field) {
            return response()->json([
                'message' => 'Field updated successfully',
                'field' => $field,
            ], Response::HTTP_OK);
        } else {
            return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(Request $request) {
        $deletedField = Fields::where('id', $request->id)->where('company_id', $request->get_company_id)->update([
            'is_deleted' => 1
        ]);

        if ($deletedField) {
            return response()->json([
                'message' => 'Field deleted successfully',
                'field' => $deletedField,
            ], Response::HTTP_OK);
        } else {
            return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }
    }
}
