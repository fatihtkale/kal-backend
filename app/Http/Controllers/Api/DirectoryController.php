<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Directory;
use App\Models\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DirectoryController extends Controller
{
    public function index(Request $request) {
        $directories = Directory::where('company_id', $request->get_company_id)->whereNull('is_deleted')->get();

        return response()->json([
            'directories' => $directories
        ]);
    }

    public function single(Request $request) {
        $directory = Directory::where('company_id', $request->get_company_id)->where('id', $request->id)->whereNull('is_deleted')->first();

        if ($directory) {
            return response()->json($directory);
        } else {
            return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function create(Request $request) {
        // Set the first as identifikation field
        $fields = $request->fields;
        $fields[0]['primary'] = true;

        $directory = Directory::create([
            'company_id' => $request->get_company_id,
            'title' => ucfirst($request->title),
            'fields' => $fields
        ]);

        if ($directory) {
            return response()->json([
                'message' => 'Directory created successfully',
                'directory' => $directory
            ], Response::HTTP_CREATED);
        } else {
            return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request) {
        $directory = Directory::where('id', $request->id)->where('company_id', $request->get_company_id)->first();
        // Update fields and title
        $directory->title = ucfirst($request->title);
        $newFields = $request->fields;

        foreach($newFields as $index => $field) {
            // Checks if new fields title is changed
            $found = array_filter($directory->fields, function($old) use ($field) {
                return $old['title'] === $field['title'];
            });

            // If the title is not found and the index length is not bigger (Means it new created and not changed)
            if (!$found && $index < count($directory->fields)) {
                // Change data parent field title to the changed one
                $oldFieldTitle = $directory->fields[$index]['title'];
                $directory->data = json_encode($directory->data);
                $directory->data = str_replace($oldFieldTitle, $field['title'], $directory->data);
                $directory->data = json_decode($directory->data);
            }
        }

        $newFields[0]['primary'] = true;
        $directory->fields = $newFields;
        $directory->save();

        if ($directory) {
            return response()->json([
                'message' => 'Field updated successfully',
                'field' => $directory,
            ], Response::HTTP_OK);
        } else {
            return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(Request $request) {
        $deletedDirectory = Directory::where('id', $request->id)->where('company_id', $request->get_company_id)->update([
            'is_deleted' => 1
        ]);

        if ($deletedDirectory) {
            return response()->json([
                'message' => 'Field deleted successfully',
                'field' => $deletedDirectory,
            ], Response::HTTP_OK);
        } else {
            return response()->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }
    }
}
