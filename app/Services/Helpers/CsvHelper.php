<?php


namespace App\Services\Helpers;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\LazyCollection;
use Illuminate\Validation\ValidationException;
use Spatie\SimpleExcel\SimpleExcelReader;

class CsvHelper
{
    /**
     * get the rows as a generator
     *
     * @param UploadedFile $csvFile
     * @param array $requiredFields
     * @return LazyCollection
     * @throws ValidationException
     */
    public static function getRows(UploadedFile $csvFile, array $requiredFields) : LazyCollection
    {
        $reader = SimpleExcelReader::create($csvFile->getRealPath(), 'csv')->trimHeaderRow();
        self::validateCsvFields($reader, $requiredFields);
        return $reader->getRows();
    }

    /**
     * Validate CSV Fields
     *
     * @return array
     * @throws ValidationException
     */
    private static function validateCsvFields(SimpleExcelReader $reader, array $requiredFields): void
    {
        $headers = $reader->getHeaders();

        $requiredFieldsExist = count(array_diff($requiredFields, $headers)) === 0;
        if (!$requiredFieldsExist) {
            throw ValidationException::withMessages(['csv' => 'CSV file is not properly formatted.']);
        }
    }

}
