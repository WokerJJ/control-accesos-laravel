<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f0f2f5;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">

{{-- Preheader (texto oculto que aparece en vista previa del inbox) --}}
<div style="display:none;font-size:1px;color:#f0f2f5;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    Bienvenido {{ $persona->primer_nombre }} — Tu registro en {{ config('app.name', 'Control de Accesos') }} fue exitoso.
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f2f5;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            {{-- Contenedor principal --}}
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:600px;width:100%;">

                {{-- Header --}}
                <tr>
                    <td align="center" style="padding:32px 0 24px 0;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center" style="padding-bottom:12px;">
                                    <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#3b82f6,#2563eb);text-align:center;line-height:64px;">
                                        <span style="font-size:32px;color:#ffffff;">🏢</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <h1 style="margin:0;font-size:24px;font-weight:700;color:#1a1a2e;letter-spacing:-0.5px;">
                                        {{ config('app.name', 'Control de Accesos') }}
                                    </h1>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Tarjeta principal --}}
                <tr>
                    <td>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.08);">

                            {{-- Banner de éxito --}}
                            <tr>
                                <td align="center" style="padding:40px 40px 24px 40px;">
                                    <div style="width:72px;height:72px;border-radius:50%;background-color:#ecfdf5;text-align:center;line-height:72px;margin:0 auto 20px auto;">
                                        <span style="font-size:36px;">✅</span>
                                    </div>
                                    <h2 style="margin:0 0 8px 0;font-size:26px;font-weight:700;color:#111827;">
                                        ¡Bienvenido, {{ $persona->primer_nombre }}!
                                    </h2>
                                    <p style="margin:0;font-size:15px;color:#6b7280;line-height:1.6;">
                                        Tu registro fue exitoso. Ya puedes acceder a todas las instalaciones.
                                    </p>
                                </td>
                            </tr>

                            {{-- Detalles del registro --}}
                            <tr>
                                <td style="padding:0 40px 32px 40px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                           style="background-color:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;">
                                        <tr>
                                            <td style="padding:24px;">
                                                <p style="margin:0 0 16px 0;font-size:13px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:0.5px;">
                                                    Detalles de tu registro
                                                </p>

                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td style="padding:8px 0;border-bottom:1px solid #e5e7eb;width:130px;">
                                                            <span style="font-size:13px;color:#9ca3af;">Nombre</span>
                                                        </td>
                                                        <td style="padding:8px 0;border-bottom:1px solid #e5e7eb;">
                                                            <span style="font-size:14px;color:#111827;font-weight:500;">{{ $persona->nombre_completo }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:8px 0;border-bottom:1px solid #e5e7eb;">
                                                            <span style="font-size:13px;color:#9ca3af;">Documento</span>
                                                        </td>
                                                        <td style="padding:8px 0;border-bottom:1px solid #e5e7eb;">
                                                            <span style="font-size:14px;color:#111827;font-weight:500;">{{ $persona->doc_identidad }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:8px 0;border-bottom:1px solid #e5e7eb;">
                                                            <span style="font-size:13px;color:#9ca3af;">Correo</span>
                                                        </td>
                                                        <td style="padding:8px 0;border-bottom:1px solid #e5e7eb;">
                                                            <span style="font-size:14px;color:#111827;font-weight:500;">{{ $persona->email }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:8px 0;">
                                                            <span style="font-size:13px;color:#9ca3af;">Fecha</span>
                                                        </td>
                                                        <td style="padding:8px 0;">
                                                            <span style="font-size:14px;color:#111827;font-weight:500;">{{ $persona->fecha_registro->translatedFormat('l, d \d\e F \d\e Y') }} — {{ $persona->created_at->format('h:i A') }}</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Próximos pasos --}}
                            <tr>
                                <td style="padding:0 40px 32px 40px;">
                                    <h3 style="margin:0 0 16px 0;font-size:18px;font-weight:600;color:#111827;">
                                        ¿Qué sigue?
                                    </h3>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="padding:12px 0;">
                                                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td style="width:36px;vertical-align:top;">
                                                            <div style="width:32px;height:32px;border-radius:8px;background-color:#eff6ff;text-align:center;line-height:32px;">
                                                                <span style="font-size:16px;">🔑</span>
                                                            </div>
                                                        </td>
                                                        <td style="padding-left:12px;vertical-align:top;">
                                                            <p style="margin:0 0 2px 0;font-size:14px;font-weight:600;color:#111827;">
                                                                Identifícate al llegar
                                                            </p>
                                                            <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">
                                                                Presenta tu documento de identidad en el acceso y serás ubicado automáticamente.
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:12px 0;">
                                                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td style="width:36px;vertical-align:top;">
                                                            <div style="width:32px;height:32px;border-radius:8px;background-color:#f0fdf4;text-align:center;line-height:32px;">
                                                                <span style="font-size:16px;">📋</span>
                                                            </div>
                                                        </td>
                                                        <td style="padding-left:12px;vertical-align:top;">
                                                            <p style="margin:0 0 2px 0;font-size:14px;font-weight:600;color:#111827;">
                                                                Selecciona tu actividad
                                                            </p>
                                                            <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">
                                                                Elige entre las actividades disponibles para tu visita.
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:12px 0;">
                                                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td style="width:36px;vertical-align:top;">
                                                            <div style="width:32px;height:32px;border-radius:8px;background-color:#fefce8;text-align:center;line-height:32px;">
                                                                <span style="font-size:16px;">📦</span>
                                                            </div>
                                                        </td>
                                                        <td style="padding-left:12px;vertical-align:top;">
                                                            <p style="margin:0 0 2px 0;font-size:14px;font-weight:600;color:#111827;">
                                                                Recibe tu casillero
                                                            </p>
                                                            <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">
                                                                Se te asignará un casillero para guardar tus pertenencias durante la visita.
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- CTA Button --}}
                            <tr>
                                <td align="center" style="padding:0 40px 40px 40px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td align="center"
                                                style="border-radius:12px;background:linear-gradient(135deg,#3b82f6,#2563eb);">
                                                <a href="{{ url('/') }}"
                                                   style="display:inline-block;padding:14px 40px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.3px;">
                                                    Ir al sistema →
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:32px 0 0 0;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center">
                                    <p style="margin:0 0 8px 0;font-size:13px;color:#9ca3af;">
                                        {{ config('app.name', 'Control de Accesos') }} — Sistema de Gestión de Accesos
                                    </p>
                                    <p style="margin:0;font-size:12px;color:#d1d5db;">
                                        Este es un correo automático. Por favor, no respondas a este mensaje.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
