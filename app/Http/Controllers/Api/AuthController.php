<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Models\{Device,User}; use Illuminate\Http\Request; use Illuminate\Support\Facades\Hash; use Illuminate\Validation\ValidationException;
class AuthController extends Controller {
 public function login(Request $r){$d=$r->validate(['email'=>'required|email','password'=>'required','device_name'=>'required|string|max:100','device_key'=>'nullable|string|max:190','platform'=>'nullable|string|max:30']);$u=User::where('email',$d['email'])->first();if(!$u||!Hash::check($d['password'],$u->password)||!$u->is_active)throw ValidationException::withMessages(['email'=>'Invalid credentials.']);
  $device=null;
  if(!empty($d['device_key'])&&$u->company_id){$device=Device::withoutGlobalScopes()->firstOrCreate(['company_id'=>$u->company_id,'device_key'=>$d['device_key']],['name'=>$d['device_name'],'platform'=>$d['platform']??'android']);if($device->disabled_at)throw ValidationException::withMessages(['device_key'=>'This device has been disabled. Contact your administrator.']);$device->update(['name'=>$d['device_name'],'user_id'=>$u->id,'last_seen_at'=>now()]);}
  $u->update(['last_login_at'=>now()]);$token=$u->createToken($d['device_name'],['mobile']);if($device){$token->accessToken->device_id=$device->id;$token->accessToken->save();}
  return response()->json(['data'=>['token'=>$token->plainTextToken,'user'=>$u->only('uuid','name','email','role','company_id'),'device'=>$device?->only('uuid','name','approved_at','disabled_at')]]);}
 public function me(Request $r){return response()->json(['data'=>$r->user()->load('company')]);}
 public function logout(Request $r){$r->user()->currentAccessToken()?->delete();return response()->noContent();}
 public function registerDevice(Request $r){$d=$r->validate(['device_key'=>'required|string|max:190','name'=>'required|string|max:100','platform'=>'nullable|string|max:30']);$device=Device::updateOrCreate(['company_id'=>$r->user()->company_id,'device_key'=>$d['device_key']],[...$d,'user_id'=>$r->user()->id,'last_seen_at'=>now()]);return response()->json(['data'=>$device],201);}
}
