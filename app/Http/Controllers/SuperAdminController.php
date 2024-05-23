<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\User;
use App\Models\AbstractUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $superAdminName = auth()->user()->name;

        // Fetch the count of non-super-admin users
        $nonSuperAdminCount = User::where('role', '!=', 'super_admin')->count();

        // Fetch total number of abstracts submitted
        $totalAbstractCount = AbstractUpload::count();

        // Fetch counts of abstracts submitted for each theme
        $themeCounts = AbstractUpload::select('theme', DB::raw('count(*) as count'))
            ->groupBy('theme')
            ->pluck('count', 'theme');

        
        return view('super_admin.dashboard', [
            'superAdminName' => $superAdminName,
            'totalAbstractCount' => $totalAbstractCount,
            'themeCounts' => $themeCounts,
            'nonSuperAdminCount' => $nonSuperAdminCount,
        ]);
    }

    // New method to fetch list of non-super-admin users
    public function userList()
    {
        // Fetch non-super-admin users
        $nonSuperAdminUsers = User::where('role', '!=', 'super_admin')->get();

        // Pass the list of non-super-admin users to the userlist view
        return view('super_admin.userlist', [
            'nonSuperAdminUsers' => $nonSuperAdminUsers
        ]);
    }
    
    // Method to fetch the abstracts uploaded in the data
    public function abstractReview()
    {
        // Check if the method is being called
        dd('abstractReview method called');

        // Check if the user is authenticated
        if (auth()->check()) {
            // Fetch the super admin name here
            $superAdminName = auth()->user()->name;

            // Fetch all abstract uploads
            $abstractUploads = AbstractUpload::all();

            // Pass the data to the view
            return view('super_admin.abstractreview', compact('superAdminName', 'abstractUploads'));
        } else {
            // Redirect the user to the login page
            return redirect()->route('login');
        }
    }
    // Method to download all abstract files as a zip
    public function downloadAbstracts()
    {
        // Get all abstract uploads
        $abstractUploads = AbstractUpload::all();

        // Create a new zip archive
        $zip = new ZipArchive;
        $zipFileName = 'abstracts.zip';
        $zipFilePath = storage_path($zipFileName);

        // Open the zip file for writing
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Add each abstract file to the zip archive
            foreach ($abstractUploads as $abstractUpload) {
                $filePath = storage_path('app/abstracts/' . basename($abstractUpload->file_path));
                $zip->addFile($filePath, basename($abstractUpload->file_path));
            }

            // Close the zip archive
            $zip->close();

            // Return the zip file for download
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            // Return an error response if unable to create the zip archive
            return response()->json(['error' => 'Failed to create zip archive'], 500);
        }
    }

    // Status Update of Abstract

    public function updateAbstractStatus(Request $request)
    {
        $request->validate([
            'abstract_id' => 'required|exists:abstract_uploads,id',
            'status' => 'required|in:Oral,Poster,Not Accepted,Pending', // Include 'Pending' status
        ]);

        $abstract = AbstractUpload::findOrFail($request->input('abstract_id'));
        $abstract->status = $request->input('status');
        $abstract->save();

        return response()->json(['message' => 'Abstract status updated successfully']);
    }

    // EXPORT ABSTRACTS

    public function exportAbstracts()
    {
        $abstracts = AbstractUpload::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'URN');
        $sheet->setCellValue('B1', 'Abstract ID');
        $sheet->setCellValue('C1', 'Name');
        $sheet->setCellValue('D1', 'Organization');
        $sheet->setCellValue('E1', 'Theme Selected');
        $sheet->setCellValue('F1', 'File Uploaded');
        $sheet->setCellValue('G1', 'Status');

        // Fill data
        $row = 2;
        foreach ($abstracts as $abstract) {
            $sheet->setCellValue('A' . $row, 'IIMATM2024_' . $abstract->user_id);
            $sheet->setCellValue('B' . $row, $abstract->abstract_upload_id);
            $sheet->setCellValue('C' . $row, $abstract->name);
            $sheet->setCellValue('D' . $row, $abstract->organization_name);
            $sheet->setCellValue('E' . $row, $abstract->theme);
            $sheet->setCellValue('F' . $row, $abstract->file_path);
            $sheet->setCellValue('G' . $row, $abstract->status);
            $row++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        $fileName = 'abstracts.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ];

        // Output the file
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return Response::make($content, 200, $headers);
    }


    // IMPORT EXCEL

    public function importAbstracts(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx'
        ]);

        $file = $request->file('excel_file');

        // Load the Excel file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();

        // Initialize a counter to track updates
        $updateCount = 0;

        // Iterate through rows
        foreach ($sheet->getRowIterator() as $row) {
            // Skip header row
            if ($row->getRowIndex() == 1) {
                continue;
            }

            $rowData = [];
            // Iterate through cells in the row
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Debug: Print row data
            logger()->info('Row Data:', $rowData);

            // Extract the identifier part "SCA_{id}" from the second column
            $abstractIdentifier = $rowData[1]; // Assuming Abstract ID is in the second column
            $abstractParts = explode('_', $abstractIdentifier);

            if (count($abstractParts) >= 3) {
                $themePart = $abstractParts[1];
                $idPart = $abstractParts[2];
            } else {
                // Log warning if the identifier format is unexpected
                logger()->warning('Unexpected Abstract Identifier Format:', [$abstractIdentifier]);
                continue;
            }

            // Debug: Print extracted abstract identifier parts
            logger()->info('Extracted Abstract Identifier:', ['theme' => $themePart, 'id' => $idPart]);

            // Find abstract by both theme and id
            $abstract = AbstractUpload::where('theme', $themePart)
                                    ->where('abstract_upload_id', $abstractIdentifier)
                                    ->first();

            if ($abstract) {
                // Debug: Print current status before update
                logger()->info('Current Status:', [$abstract->status]);

                // Update abstract status
                $newStatus = $rowData[6]; // Assuming Status is in the seventh column

                // Only update if the status has changed
                if ($abstract->status !== $newStatus) {
                    $abstract->status = $newStatus;
                    $abstract->save();
                    $updateCount++;

                    // Debug: Print new status after update
                    logger()->info('Updated Status:', [$abstract->status]);
                }
            } else {
                // Debug: Log that the abstract was not found
                logger()->warning('Abstract not found for Identifier:', [$themePart . '_' . $idPart]);
            }
        }

        // Return a success message with the count of updated abstracts
        return redirect()->back()->with('success', "{$updateCount} abstract statuses updated successfully");
    }






}
