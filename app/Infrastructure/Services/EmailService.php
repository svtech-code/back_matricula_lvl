<?php

namespace App\Infrastructure\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUsername;
    private string $smtpPassword;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $this->smtpPort = (int)($_ENV['SMTP_PORT'] ?? 587);
        $this->smtpUsername = $_ENV['SMTP_USERNAME'] ?? '';
        $this->smtpPassword = $_ENV['SMTP_PASSWORD'] ?? '';
        $this->fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? '';
        $this->fromName = $_ENV['SMTP_FROM_NAME'] ?? 'Sistema de Matrículas';
    }

    public function enviarCorreoPrematricula(array $datosEstudiante, array $datosApoderado): bool
    {
        if (empty($this->smtpUsername) || empty($this->smtpPassword) || empty($this->fromEmail)) {
            throw new Exception('Configuración SMTP incompleta. Verifique las variables SMTP_USERNAME, SMTP_PASSWORD y SMTP_FROM_EMAIL en el archivo .env');
        }

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0;

            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->smtpPort;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($datosApoderado['email'], $datosApoderado['nombre_completo']);

            $mail->isHTML(true);
            $mail->Subject = 'Confirmación de Prematrícula - ' . $datosEstudiante['nombres'] . ' ' . $datosEstudiante['apellido_paterno'];
            $mail->Body = $this->generarCuerpoCorreo($datosEstudiante, $datosApoderado);
            $mail->AltBody = $this->generarCuerpoCorreoTextoPlano($datosEstudiante, $datosApoderado);

            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al enviar correo: ' . $e->getMessage() . ' | Info: ' . $mail->ErrorInfo);
        }
    }

    private function generarCuerpoCorreo(array $estudiante, array $apoderado): string
    {
        $documentosHtml = $this->generarSeccionDocumentos();

        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: #f4f4f4;
                    padding: 20px;
                }
                .container {
                    max-width: 650px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                .header {
                    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
                    color: white;
                    padding: 40px 30px;
                    text-align: center;
                }
                .header h1 {
                    font-size: 24px;
                    margin-bottom: 10px;
                    font-weight: 600;
                }
                .header p {
                    font-size: 14px;
                    opacity: 0.9;
                }
                .content {
                    padding: 30px;
                }
                .greeting {
                    font-size: 16px;
                    margin-bottom: 20px;
                    color: #555;
                }
                .success-message {
                    background-color: #d4edda;
                    border-left: 4px solid #28a745;
                    padding: 15px;
                    margin-bottom: 25px;
                    border-radius: 4px;
                }
                .success-message p {
                    color: #155724;
                    margin: 0;
                    font-weight: 500;
                }
                .section {
                    margin-bottom: 25px;
                }
                .section-title {
                    font-size: 18px;
                    font-weight: 600;
                    color: #1e3c72;
                    margin-bottom: 15px;
                    padding-bottom: 8px;
                    border-bottom: 2px solid #e0e0e0;
                }
                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .info-table tr {
                    border-bottom: 1px solid #f0f0f0;
                }
                .info-table tr:last-child {
                    border-bottom: none;
                }
                .info-table td {
                    padding: 10px 0;
                    font-size: 14px;
                }
                .info-table td:first-child {
                    font-weight: 600;
                    color: #555;
                    width: 45%;
                }
                .info-table td:last-child {
                    color: #333;
                }
                .documents-section {
                    background-color: #f8f9fa;
                    padding: 20px;
                    border-radius: 6px;
                    margin-top: 25px;
                }
                .documents-title {
                    font-size: 18px;
                    font-weight: 600;
                    color: #1e3c72;
                    margin-bottom: 15px;
                    padding-bottom: 8px;
                    border-bottom: 2px solid #e0e0e0;
                }
                .document-item {
                    display: table;
                    width: 100%;
                    background-color: #ffffff;
                    padding: 15px;
                    margin-bottom: 10px;
                    border-radius: 6px;
                    border: 1px solid #e0e0e0;
                    text-decoration: none;
                    transition: all 0.3s ease;
                }
                .document-item:hover {
                    border-color: #2a5298;
                    box-shadow: 0 2px 6px rgba(42, 82, 152, 0.15);
                }
                .document-info {
                    display: table-cell;
                    vertical-align: middle;
                    padding-right: 15px;
                }
                .document-name {
                    font-weight: 600;
                    color: #333;
                    font-size: 14px;
                    margin-bottom: 3px;
                }
                .document-hint {
                    font-size: 12px;
                    color: #666;
                }
                .document-action {
                    display: table-cell;
                    width: 120px;
                    vertical-align: middle;
                    text-align: right;
                }
                .download-button {
                    display: inline-block;
                    background-color: #2a5298;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 4px;
                    font-size: 13px;
                    font-weight: 500;
                    text-decoration: none;
                    white-space: nowrap;
                }
                .download-button:hover {
                    background-color: #1e3c72;
                }
                @media only screen and (max-width: 600px) {
                    .document-item {
                        display: block !important;
                        padding: 12px;
                    }
                    .document-info {
                        display: block !important;
                        padding-right: 0;
                        margin-bottom: 10px;
                    }
                    .document-action {
                        display: block !important;
                        width: 100% !important;
                        text-align: center;
                    }
                    .download-button {
                        display: block;
                        width: 100%;
                        text-align: center;
                    }
                }
                .document-item:hover {
                    border-color: #2a5298;
                    box-shadow: 0 2px 6px rgba(42, 82, 152, 0.15);
                }
                .document-icon {
                    display: table-cell;
                    width: 50px;
                    vertical-align: middle;
                }
                .pdf-icon {
                    width: 40px;
                    height: 40px;
                    background-color: #dc3545;
                    border-radius: 6px;
                    display: block;
                    text-align: center;
                    line-height: 40px;
                    font-size: 20px;
                    color: white;
                }

                .document-info {
                    display: table-cell;
                    vertical-align: middle;
                    padding: 0 15px;
                }
                .document-name {
                    font-weight: 600;
                    color: #333;
                    font-size: 14px;
                    margin-bottom: 3px;
                }
                .document-hint {
                    font-size: 12px;
                    color: #666;
                }
                .document-action {
                    display: table-cell;
                    width: 120px;
                    vertical-align: middle;
                    text-align: right;
                }
                .download-button {
                    display: inline-block;
                    background-color: #2a5298;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 4px;
                    font-size: 13px;
                    font-weight: 500;
                    text-decoration: none;
                    white-space: nowrap;
                }
                .download-button:hover {
                    background-color: #1e3c72;
                }
                .next-steps {
                    background-color: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin-top: 25px;
                    border-radius: 4px;
                }
                .next-steps h3 {
                    color: #856404;
                    font-size: 15px;
                    margin-bottom: 10px;
                    font-weight: 600;
                }
                .next-steps ul {
                    margin-left: 20px;
                    color: #856404;
                }
                .next-steps li {
                    margin-bottom: 5px;
                    font-size: 13px;
                }
                .footer {
                    background-color: #f8f9fa;
                    padding: 20px 30px;
                    text-align: center;
                    border-top: 1px solid #e0e0e0;
                }
                .footer p {
                    font-size: 12px;
                    color: #666;
                    margin-bottom: 5px;
                }
                .footer strong {
                    color: #1e3c72;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✓ Prematrícula Registrada Exitosamente</h1>
                    <p>Sistema de Matrículas en Línea</p>
                </div>
                
                <div class='content'>
                    <p class='greeting'>Estimado/a <strong>{$apoderado['nombre_completo']}</strong>,</p>
                    
                    <div class='success-message'>
                        <p>✓ La prematrícula ha sido registrada correctamente en nuestro sistema.</p>
                    </div>
                    
                    <div class='section'>
                        <div class='section-title'>📋 Datos del Estudiante</div>
                        <table class='info-table'>
                            <tr>
                                <td>Nombre completo:</td>
                                <td>{$estudiante['nombres']} {$estudiante['apellido_paterno']} {$estudiante['apellido_materno']}</td>
                            </tr>
                            <tr>
                                <td>RUT:</td>
                                <td>{$estudiante['rut']}</td>
                            </tr>
                            <tr>
                                <td>Curso a matricular:</td>
                                <td>{$estudiante['grado_a_matricularse']}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class='section'>
                        <div class='section-title'>👤 Datos del Apoderado Titular</div>
                        <table class='info-table'>
                            <tr>
                                <td>Nombre completo:</td>
                                <td>{$apoderado['nombres']} {$apoderado['apellido_paterno']} {$apoderado['apellido_materno']}</td>
                            </tr>
                            <tr>
                                <td>RUT:</td>
                                <td>{$apoderado['rut']}</td>
                            </tr>
                            <tr>
                                <td>Relación:</td>
                                <td>{$apoderado['tipo_familiar']}</td>
                            </tr>
                        </table>
                    </div>
                    
                    {$documentosHtml}
                    
                    <div class='next-steps'>
                        <h3>📌 Próximos Pasos</h3>
                        <ul>
                            <li>Revise y descargue la documentación institucional adjunta</li>
                            <li>Mantenga este correo como comprobante de su prematrícula</li>
                            <li>Estaremos en contacto para confirmar el proceso de matrícula definitiva</li>
                        </ul>
                    </div>
                </div>
                
                <div class='footer'>
                    <p><strong>{$this->fromName}</strong></p>
                    <p>Este es un correo automático, por favor no responder.</p>
                    <p>Si tiene consultas, contáctese con la institución.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function generarSeccionDocumentos(): string
    {
        $directorioDocumentos = __DIR__ . '/../../../public/documents/institucional';

        if (!is_dir($directorioDocumentos)) {
            return '';
        }

        $archivos = scandir($directorioDocumentos);
        $documentosItems = '';

        $baseUrl = $_ENV['APP_URL'] ?? getenv('APP_URL') ?? 'https://api.lvl.cl';

        foreach ($archivos as $archivo) {
            if ($archivo === '.' || $archivo === '..' || $archivo === '.htaccess') {
                continue;
            }

            $rutaCompleta = $directorioDocumentos . '/' . $archivo;

            if (is_file($rutaCompleta)) {
                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                if ($extension !== 'pdf') {
                    continue;
                }

                $urlDescarga = $baseUrl . "/documents/institucional/" . rawurlencode($archivo);

                $nombreArchivo = pathinfo($archivo, PATHINFO_FILENAME);

                $documentosItems .= "
                <a href='{$urlDescarga}' class='document-item' target='_blank' style='text-decoration: none; color: inherit;'>
                    <div class='document-info'>
                        <div class='document-name'>{$nombreArchivo}</div>
                        <div class='document-hint'>Haga clic para descargar el documento</div>
                    </div>
                    <div class='document-action'>
                        <span class='download-button'>📄 Ver PDF</span>
                    </div>
                </a>
                ";
            }
        }

        if (empty($documentosItems)) {
            return '';
        }

        return "
        <div class='documents-section'>
            <div class='documents-title'>📄 Documentación Institucional</div>
            <p style='font-size: 13px; color: #666; margin-bottom: 15px;'>Descargue los siguientes documentos institucionales:</p>
            {$documentosItems}
        </div>
        ";
    }

    private function generarCuerpoCorreoTextoPlano(array $estudiante, array $apoderado): string
    {
        return "
PREMATRÍCULA REGISTRADA CON ÉXITO

Estimado/a {$apoderado['nombre_completo']},

La prematrícula ha sido registrada correctamente en nuestro sistema.

DATOS DEL ESTUDIANTE:
- Nombre completo: {$estudiante['nombres']} {$estudiante['apellido_paterno']} {$estudiante['apellido_materno']}
- RUT: {$estudiante['rut']}
- Curso a matricular: {$estudiante['grado_a_matricularse']}

DATOS DEL APODERADO TITULAR:
- Nombre completo: {$apoderado['nombres']} {$apoderado['apellido_paterno']} {$apoderado['apellido_materno']}
- RUT: {$apoderado['rut']}
- Relación: {$apoderado['tipo_familiar']}

PRÓXIMOS PASOS:
- Revise y descargue la documentación institucional
- Mantenga este correo como comprobante de su prematrícula
- Estaremos en contacto para confirmar el proceso de matrícula definitiva

---
{$this->fromName}
Este es un correo automático, por favor no responder.
        ";
    }
}
