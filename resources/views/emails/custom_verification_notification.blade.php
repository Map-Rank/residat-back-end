
<h2>Vérification d'Email</h2>
<p>Nous vous remercions de vous être inscrit sur notre plateforme. </p> 
<p>Si vous consultez cet e-mail sur un ordinateur, cliquez sur le bouton ci-dessous pour vérifier votre adresse e-mail :</p>
<div>
    <a class="btn btn-primary" href="{{ config('app.front_url') . '/verify-email/' . $data['id'] . '/' . $data['hash'] }}">Vérifier l'Email</a>
</div>
