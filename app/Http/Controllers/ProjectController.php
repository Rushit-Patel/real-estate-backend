<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectContact;
use App\Models\Property;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Ramsey\Uuid\Uuid;
use Validator;

class ProjectController extends Controller
{
    public function index()
    {
        
        return response()->json(Project::with(['contacts', 'properties', 'files'])->get());
    }

    public function store(Request $request)
    { 
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'status' => 'string|in:Active,Inactive',
            'map_location_url' => 'url|nullable',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'address_line_3' => 'string|nullable',
            'assign_to_id' => 'string|nullable',
            'area' => 'string|nullable',
            'city' => 'string|nullable',
            'contacts' => 'array|nullable',
            'contacts.*.contact_no' => 'string',
            'properties' => 'array|nullable',
            'properties.*.name' => 'string',
            'properties.*.property_type_id' => 'exists:property_types,id',
            'properties.*.quantity' => 'integer|min:1',
            'files' => 'array|nullable',
            'files.*.file_name' => 'string|nullable',
            'files.*.newFile' => 'file|mimes:pdf,jpg,png|max:2048',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();


        $project = Project::create($request->only([
            'name', 'status', 'map_location_url', 'address_line_1','address_line_2','address_line_3','assign_to_id', 'area','city', 'public_id'
        ]));

        $project->update([
            'public_id' => rand(1000, 9999).'69' . $project->id
        ]);

        if ($request->contacts) {
            foreach ($request->contacts as $contact) {
                ProjectContact::create([
                    'project_id' => $project->id,
                    'contact_no' => $contact['contact_no'],
                ]);
            }
        }

        if ($request->properties) {
            foreach ($request->properties as $property) {
                Property::create([
                    'project_id' => $project->id,
                    'name' => $property['name'],
                    'property_type_id' => $property['property_type_id'],
                    'quantity' => $property['quantity'],
                ]);
            }
        }

        if (!empty($data['files'])) {
            foreach ($data['files'] as $fileData) {
                if (!empty($fileData['newFile']) && $fileData['newFile']->isValid()) {
                    $originalName = $fileData['newFile']->getClientOriginalName();
                    $extension = $fileData['newFile']->getClientOriginalExtension(); // Get file type (e.g. png, jpg)
                    $baseName = $fileData['file_name'] ?? 'file';

                    if (empty($extension)) {
                        $extension = $fileData['newFile']->guessExtension();
                    }

                    $sanitizedBaseName = preg_replace('/[^A-Za-z0-9_-]/', '_', $baseName);
                    $customName = $sanitizedBaseName . '_' . time() . '.' . $extension;

                    $path = $fileData['newFile']->storeAs('projects', $customName, 'public');

                    ProjectFile::create([
                        'project_id' => $project->id,
                        'file_name' => $baseName,
                        'file_path' => $path,
                    ]);
                }
            }
        }


        return response()->json(['message' => 'Project created', 'data' => $project->load(['contacts', 'properties', 'files'])], 201);
    }

    public function show(Project $project)
    {
        
        return response()->json($project->load(['contacts', 'properties', 'files']));
    }

    public function update(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'string|max:255',
            'status' => 'string|in:Active,Inactive',
            'map_location_url' => 'url|nullable',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'address_line_3' => 'string|nullable',
            'assign_to_id' => 'string|nullable',
            'area' => 'string|nullable',
            'city' => 'string|nullable',
            'contacts' => 'array|nullable',
            'contacts.*.contact_no' => 'string',
            'properties' => 'array|nullable',
            'properties.*.name' => 'string',
            'properties.*.property_type_id' => 'exists:property_types,id',
            'properties.*.quantity' => 'integer|min:1',
            'files' => 'array|nullable',
            'files.*.file_name' => 'string|nullable',
            'files.*.newFile' => 'file|mimes:pdf,jpg,png|max:2048',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $request->all();

        $project->update($request->only([
            'name', 'status', 'map_location_url', 'address_line_1','address_line_2','address_line_3','assign_to_id','area', 'city'
        ]));

        if ($request->contacts) {
            $existingContactIds = $project->contacts()->pluck('id')->toArray();
            $incomingContactIds = [];

            foreach ($request->contacts as $contact) {
                if (!empty($contact['id']) && in_array($contact['id'], $existingContactIds)) {
                    ProjectContact::where('id', $contact['id'])->update([
                        'contact_no' => $contact['contact_no'],
                    ]);
                    $incomingContactIds[] = $contact['id'];
                } else {
                    $newContact = ProjectContact::create([
                        'project_id' => $project->id,
                        'contact_no' => $contact['contact_no'],
                    ]);
                    $incomingContactIds[] = $newContact->id;
                }
            }

            $contactsToDelete = array_diff($existingContactIds, $incomingContactIds);
            if (!empty($contactsToDelete)) {
                ProjectContact::whereIn('id', $contactsToDelete)->delete();
            }
        }

        if ($request->properties) {
            $existingPropertyIds = $project->properties()->pluck('id')->toArray();
            $incomingPropertyIds = [];

            foreach ($request->properties as $property) {
                if (!empty($property['id']) && in_array($property['id'], $existingPropertyIds)) {
                    Property::where('id', $property['id'])->update([
                        'name' => $property['name'],
                        'property_type_id' => $property['property_type_id'],
                        'quantity' => $property['quantity'],
                    ]);
                    $incomingPropertyIds[] = $property['id'];
                } else {
                    $newProperty = Property::create([
                        'project_id' => $project->id,
                        'name' => $property['name'],
                        'property_type_id' => $property['property_type_id'],
                        'quantity' => $property['quantity'],
                    ]);
                    $incomingPropertyIds[] = $newProperty->id;
                }
            }

            $propertiesToDelete = array_diff($existingPropertyIds, $incomingPropertyIds);
            if (!empty($propertiesToDelete)) {
                Property::whereIn('id', $propertiesToDelete)->delete();
            }
        }


        // === FILES UPDATE LOGIC ===
  
        if ($data['files']) {
            $existingFileIds = $project->files()->pluck('id')->toArray(); 
            $incomingFileIds = []; 

            foreach ($data['files'] as $fileData) {
                if (isset($fileData['id']) && !empty($fileData['id']) ) {
                    $file = ProjectFile::find($fileData['id']);

                    if (!empty($fileData['newFile']) && $fileData['newFile']->isValid()) {
                        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                            Storage::disk('public')->delete($file->file_path);
                        }

                        $extension = $fileData['newFile']->getClientOriginalExtension();
                        $baseName = $fileData['file_name'] ?? 'file';
                        $sanitizedBaseName = preg_replace('/[^A-Za-z0-9_-]/', '_', $baseName);
                        if (empty($extension)) {
                            $extension = $fileData['newFile']->guessExtension();
                        }
                        $customName = $sanitizedBaseName . '_' . time() . '.' . $extension;
                        $path = $fileData['newFile']->storeAs('projects', $customName, 'public');

                        $file->update([
                            'file_name' => $baseName,
                            'file_path' => $path,
                        ]);
                    } else {
                        $file->update([
                            'file_name' => $fileData['file_name'],
                        ]);
                    }

                    $incomingFileIds[] = $file->id;
                }

                elseif (!empty($fileData['newFile']) && $fileData['newFile']->isValid()) {
                    $extension = $fileData['newFile']->getClientOriginalExtension();
                    if (empty($extension)) {
                        $extension = $fileData['newFile']->guessExtension();
                    }
                    $baseName = $fileData['file_name'] ?? 'file';
                    $sanitizedBaseName = preg_replace('/[^A-Za-z0-9_-]/', '_', $baseName);
                    $customName = $sanitizedBaseName . '_' . time() . '.' . $extension;
                    $path = $fileData['newFile']->storeAs('projects', $customName, 'public');

                    $newFile = ProjectFile::create([
                        'project_id' => $project->id,
                        'file_name' => $baseName,
                        'file_path' => $path,
                    ]);

                    $incomingFileIds[] = $newFile->id;
                }
            }

            $filesToDelete = array_diff($existingFileIds, $incomingFileIds);
            if (!empty($filesToDelete)) {
                $filesToRemove = ProjectFile::whereIn('id', $filesToDelete)->get();
                foreach ($filesToRemove as $file) {
                    if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                        Storage::disk('public')->delete($file->file_path);
                    }
                }
                ProjectFile::whereIn('id', $filesToDelete)->delete();
            }
        }

        return response()->json(['message' => 'Project updated', 'data' => $project->load(['contacts', 'properties', 'files'])]);
    }

    public function destroy(Project $project)
    {
        
        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }

    public function uploadFile(Request $request, Project $project)
    {
        
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->store('projects', 'public');

        $projectFile = ProjectFile::create([
            'project_id' => $project->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
        ]);

        return response()->json(['message' => 'File uploaded', 'data' => $projectFile], 201);
    }

    public function showPublic($publicId)
    {
        $project = Project::where('public_id', $publicId)->with(['contacts', 'properties', 'files'])->firstOrFail();
        return response()->json($project);
    }
}