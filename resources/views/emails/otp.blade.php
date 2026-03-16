<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de e-mail</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding:40px 40px 24px;">
                            <span style="font-size:22px;font-weight:700;color:#111827;letter-spacing:-0.5px;">Comissify</span>
                        </td>
                    </tr>

                    <!-- Title -->
                    <tr>
                        <td align="center" style="padding:0 40px 16px;">
                            <h1 style="margin:0;font-size:20px;font-weight:700;color:#111827;">Verifique seu e-mail</h1>
                        </td>
                    </tr>

                    <!-- Description -->
                    <tr>
                        <td align="center" style="padding:0 40px 32px;">
                            <p style="margin:0;font-size:14px;color:#6b7280;line-height:1.6;text-align:center;">
                                Recebemos uma tentativa de cadastro com o código abaixo.<br>
                                Insira-o na janela do navegador onde você iniciou o cadastro.
                            </p>
                        </td>
                    </tr>

                    <!-- OTP Code -->
                    <tr>
                        <td align="center" style="padding:0 40px 32px;">
                            <div style="background:#f9fafb;border-radius:8px;padding:24px 40px;display:inline-block;">
                                <span style="font-size:36px;font-weight:700;color:#111827;letter-spacing:8px;">{{ $code }}</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Disclaimer -->
                    <tr>
                        <td align="center" style="padding:0 40px 40px;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;line-height:1.6;text-align:center;">
                                Se você não tentou se cadastrar mas recebeu este e-mail, ignore-o.<br>
                                O código expira em <strong>15 minutos</strong>.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding:24px 40px;border-top:1px solid #f3f4f6;">
                            <p style="margin:0;font-size:12px;color:#d1d5db;">© {{ date('Y') }} Comissify. Todos os direitos reservados.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
