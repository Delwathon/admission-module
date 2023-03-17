<?php

namespace Modules\Admission\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\Admission\Entities\Enrol;
use Illuminate\Contracts\Support\Renderable;

class MultipleEnrolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sessions = Session::get();
        $branches = Branch::get();

        return view('enroll.multiple', compact(['branches', 'sessions']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'session'=>'required|numeric',
            'branch'=>'required|numeric',
            'class'=>'required|numeric',
            'section'=>'required|numeric',
            'department'=>'required|numeric',
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        function get_reg_number ($short_name, $last_student) { 
            if (empty($last_student)) {
                return $short_name . ' - 1001';
            }else {
                $last_student_id = $last_student->id + 1001;
                return $short_name . ' - ' . $last_student_id;
            }
        }
    
        if ($request->hasFile('csv_file')) {
            $path = $request->file('csv_file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            array_shift($data);

            foreach ($data as $row) { 
                $short_name = Branch::where('id', $request->branch)->first()->short_name;
                $last_student = Student::latest('id')->first();
                $reg_number = get_reg_number ($short_name, $last_student);

                $randomNumber = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                $user = new User();
                $user->firstname = ucfirst($row[2]);
                $user->lastname = ucfirst($row[4]);
                $user->middlename = ucfirst($row[3]);
                $user->email = strtolower($row[2].'.'.$row[4].''.$randomNumber.'@delwathon.com');
                $user->password = Hash::make($row[4].''.date('Y').'@');
                $user->save();

                $user->assignRole(7);
                $enrol = new Enrol();
                $enrol->user_id = $user->id;
                $enrol->guardian_id = 1;
                $enrol->mobile = '';
                $enrol->blood_group = $row[8];
                $enrol->session_id = $request->session;
                $enrol->genotype = $row[9];
                $enrol->mothertongue = '';
                $enrol->mobile = '';
                $enrol->city = $row[11];
                $enrol->dob = $row[6];
                $enrol->state_id = 37;
                $enrol->local_government_id = 774;
                $enrol->address = $row[10];
                $enrol->gender = $row[5];
                $enrol->religion = $row[7];
                $enrol->branch_id = $request->branch;
                $enrol->department_id = $request->department;
                $enrol->regno = $reg_number; //$request->regno;
                $enrol->rollno = $row[1];  //$request->section;
                $enrol->section_id = $request->section;
                $enrol->s_class_id = $request->class;
                $enrol->enrol_date = date('Y-m-d',strtotime($row[0]));
                $enrol->save();

                $student = new Student();
                $student->user_id = $user->id;
                $student->enrol_id = $enrol->id;
                $student->enrol_date = date('Y-m-d',strtotime($row[0]));
                $student->guardian_id = 1;
                $student->mobile = '';
                $student->blood_group = $row[8];
                $student->session_id = $request->session;
                $student->genotype = $row[9];
                $student->mothertongue = '';
                $student->city = $row[11];
                $student->dob = $row[6];
                $student->state_id = 37;
                $student->local_government_id = 774;
                $student->address = $row[10];
                $student->gender = $row[5];
                $student->religion = $row[7];
                $student->branch_id = $request->branch;
                $student->department_id = $request->department;
                $student->regno = $reg_number;//$request->regno;
                $student->rollno = $row[1];  //$request->section;
                $student->section_id = $request->section;
                $student->s_class_id = $request->class;
                $student->save();
            }        
        }
        return redirect()->route('import-enroll.index')->with('success', 'Batch Admission file imported successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function download() 
    {
        $filePath = public_path("downloadable/enroll_sheet.csv");
        $headers = ['Content-Type: text/csv'];
        $fileName = 'enroll_sheet_template.csv';

        return response()->download($filePath, $fileName, $headers);
    }
}