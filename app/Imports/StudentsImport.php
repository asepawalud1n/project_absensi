<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $class = SchoolClass::where('name', $row['kelas'])->first();
        
        $gender = strtolower($row['jk']) === 'laki-laki' || strtolower($row['jk']) === 'l' ? 'L' : 'P';
        
        return new Student([
            'nis'       => $row['nis'],
            'nisn'      => $row['nisn'] ?? null,
            'name'      => $row['nama'],
            'gender'    => $gender,
            'class_id'  => $class?->id,
            'email'     => $row['email'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nis'   => 'required|unique:students,nis',
            'nama'  => 'required',
            'jk'    => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nis.required' => 'NIS wajib diisi!',
            'nis.unique'   => 'NIS sudah terdaftar!',
            'nama.required' => 'Nama wajib diisi!',
            'jk.required'  => 'Jenis Kelamin wajib diisi!',
        ];
    }
}
