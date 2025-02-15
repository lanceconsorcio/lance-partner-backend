@component('mail::message')

# Olá, {{ $user->name }}!

Recebemos um pedido para redefinir sua senha. Se você não solicitou essa alteração, ignore este e-mail.

Clique no botão abaixo para redefinir sua senha:

@component('mail::button', ['url' => $resetUrl])
Redefinir Senha
@endcomponent

Se o botão não funcionar, copie e cole o seguinte link no seu navegador:

[{{ $resetUrl }}]({{ $resetUrl }})

Atenciosamente,  
**Equipe {{ config('app.name') }}**

@endcomponent