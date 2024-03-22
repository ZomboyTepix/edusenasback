<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $ipAddress = $request->ip();

        try {
            // Validación de entrada
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users', // Verifica que el correo no exista
                'password' => 'required',
            ]);
    
            // Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            // Generar token
            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $data['user'] = $user;
    
            // Respuesta exitosa
            return response()->json([
                'status' => 'success',
                'message' => 'Usuario creado exitosamente.',
                'data' => $data,
            ], 201);
        } catch (\Exception $e) {
            Log::error("[".$ipAddress."]".'Error al crear el usuario: ' . $e->getMessage());
            // Manejo de excepciones
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        //$date = time();
        $ipAddress = $request->ip();
        try {
            // Validación de entrada
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            // Buscar usuario por correo electrónico
            $user = User::where('email', $request->email)->first();
    
            // Verificar contraseña
            if (!$user || !Hash::check($request->password, $user->password)) {
                $message = "[".$ipAddress."] Un usuario intento ingresar con contraseña incorrecta";
                Log::alert($message);
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Credenciales inválidas',
                ], 401);
            }else{
                // Generar token
            $data['token'] = $user->createToken($request->email)->plainTextToken;
            $data['user'] = $user;
    
            // Respuesta exitosa
            return response()->json([
                'status' => 'success',
                'message' => 'Usuario ha iniciado sesión exitosamente.',
                'data' => $data,
            ], 200);
            }
        } catch (\Exception $e) {
            Log::error("[".$ipAddress."]".$e->getMessage());
            // Manejo de excepciones
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage(),
            ], 500);

        }
    } 

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   /* public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User is logged out successfully'
            ], 200);
    }   */ 
}
