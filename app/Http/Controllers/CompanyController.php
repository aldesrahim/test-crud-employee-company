<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreRequest;
use App\Http\Requests\Company\UpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function __construct(protected CompanyService $companyService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return $this->companyService->get()
                ->through(fn (Company $company) => new CompanyResource($company));
        }

        return Inertia::render('Manage/Company/List');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->companyService->create($request->validated());

        return to_route('manage.company.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return response()->json([
            'data' => new CompanyResource($company),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Company $company)
    {
        $this->companyService->update($company, $request->validated());

        return to_route('manage.company.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $this->companyService->delete($company);

        return to_route('manage.company.index');
    }
}
