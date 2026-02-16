<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Senha - VM Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* CSS Personalizado */
        body {
            background-color: #09090b;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card-custom {
            background-color: #18181B;
            border: 1px solid #252525;
            border-radius: 12px;
            width: 100%;
            max-width: 512px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .brand-title {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 700; /* Peso da fonte aumentado */
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .page-title {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 800; /* Peso da fonte aumentado ainda mais */
            text-align: center;
            margin-bottom: 1.5rem;
        }

        /* Novo estilo para o parágrafo */
        .info-text {
            color: #ffffff;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .form-label {
            color: #ffffff;
            font-weight: 700; /* Peso da fonte aumentado nos labels */
            font-size: 0.9rem;
        }

        .required-asterisk {
            color: #ff4d4d;
        }

        .form-control {
            background-color: #262626;
            border: 1px solid #333;
            color: #fff;
            padding: 0.7rem;
            font-weight: 500; /* Leve aumento no peso do texto digitado */
        }

        .form-control:focus {
            background-color: #262626;
            border-color: #e67e22;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(230, 126, 34, 0.25);
        }

        .input-group-text {
            background-color: #262626;
            border: 1px solid #333;
            border-left: none;
            color: #888;
            cursor: pointer;
        }
        
        .input-group .form-control {
            border-right: none;
        }

        .btn-orange {
            background-color: #e67e22;
            border: none;
            color: #000;
            font-weight: 700; /* Peso do botão aumentado */
            padding: 0.7rem;
            width: 100%;
            margin-top: 1.5rem;
            transition: all 0.3s;
        }

        .btn-orange:hover {
            background-color: #d35400;
            color: #fff;
        }

        ::placeholder {
            color: #666 !important;
            font-weight: 400;
        }
    </style>
</head>
<body>
    <div class="card-custom">
        <div class="brand-title">VM - Sistema de Mercados</div>
        <div class="page-title">Olá, {{ $user->name }}</div>
        <p class="info-text">Para acessar o sistema, por favor, defina sua senha.</p>
        <form action="{{ route('registrar-senha.store', ['token' => $token]) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="password" class="form-label">Senha<span class="required-asterisk">*</span></label>
                <div class="input-group @error('password') border rounded border-danger @enderror">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Digite sua senha">
                    <span class="input-group-text" onclick="togglePassword('password', 'icon-password')">
                        <i class="bi bi-eye" id="icon-password"></i>
                    </span>
                </div>
                <span class="text-danger">{{ $errors->first('password') }}</span>
            </div>
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Confirmar Senha<span class="required-asterisk">*</span></label>
                <div class="input-group @error('password_confirm') border rounded border-danger @enderror">
                    <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Confirme sua senha">
                    <span class="input-group-text" onclick="togglePassword('password_confirm', 'icon-password_confirm')">
                        <i class="bi bi-eye" id="icon-password_confirm"></i>
                    </span>
                </div>
                <span class="text-danger">{{ $errors->first('password_confirm') }}</span>
            </div>
            <button type="submit" class="btn btn-orange">Salvar Senha</button>
        </form>
    </div>
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>