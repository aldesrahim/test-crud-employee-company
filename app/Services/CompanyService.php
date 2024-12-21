<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyService
{
    public function get()
    {
        return QueryBuilder::for(Company::class)
            ->allowedFilters($columns = [
                'name',
                'email',
                'website',
            ])
            ->withExists('employees')
            ->allowedSorts($columns)
            ->defaultSorts('-created_at')
            ->orderBy('id')
            ->jsonPaginate();
    }

    public function findById(int $id): Company
    {
        return Company::firstOrFail($id);
    }

    public function create(array $validated): Company
    {
        return DB::transaction(function () use ($validated) {
            $company = Company::create($validated);

            /** @var UploadedFile $uploadedFile */
            if (filled($uploadedFile = $validated['attachment_logo'] ?? null)) {
                $attachment = $this->handleAttachment($uploadedFile);
                $company->update($attachment);
            }

            return $company;
        });
    }

    public function update(Company|string $id, array $validated): Company
    {
        $company = $id instanceof Company ? $id : $this->findById($id);

        return DB::transaction(function () use ($company, $validated) {
            $company->update($validated);

            /** @var UploadedFile $uploadedFile */
            if (filled($uploadedFile = $validated['attachment_logo'] ?? null)) {
                $attachment = $this->handleAttachment($uploadedFile);
                $company->update($attachment);
            }

            return $company;
        });
    }

    public function delete(Company|string $id): void
    {
        $company = $id instanceof Company ? $id : $this->findById($id);

        DB::transaction(function () use ($company) {
            $company->delete();
            $this->handleAttachment(company: $company);
        });
    }

    protected function handleAttachment(?UploadedFile $file = null, ?Company $company = null): array
    {
        $path = $file?->store('/company-logo', ['disk' => 'public']);

        if ($company?->logo_path && $company?->logo_disk) {
            Storage::disk($company->logo_disk)->exists($company->logo_path) && Storage::disk($company->logo_disk)->delete($company->logo_path);
        }

        return [
            'logo_path' => $path,
            'logo_disk' => 'public',
        ];
    }
}
