<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Http\Requests\RegisterTenantRequest;

class RegisterdTenantController extends Controller
{
    public function create()
    {
        return view('register_tenant');
    }

    public function store(RegisterTenantRequest $request)
    {

        $tenant = Tenant::create($request->validated());

        $tenant->createDomain(['domain' => $request->domain]);

        $redirectUrl = '/admin/dashboard';       

        $token = tenancy()->impersonate($tenant, 1, $redirectUrl, 'admin');       

        return redirect(tenant_route($tenant->domains->first()->domain, 'tenant.admin.impersonation', $token));
             

    }
}
