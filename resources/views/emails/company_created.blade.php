<!DOCTYPE html>
<html>
<head>
    <title>Company Created</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
</head>
<body>
    <section id="about">
        <div class="img-cont">
            <img class="img" src="{{asset('assets/brand/logo.jpg')}}">
        </div>
        <h3>Request registered</h3>
        <p>Dear <strong class="color-text">{{ $company->owner_name }},</strong></p>
        <p>Your request for<strong class="color-text"> "{{ $company->company_name }}"</strong> has Been registered. Our team will revert to you as soon as possible .</p>
        <p>Thanks for using our app.</p>
    
        <h4>Follow Us On Social Media</h4>
    
        <div class="social">
        {{-- <a href="https://twitter.com/traversymedia" target="_blank"><i class="fab fa-twitter fa-3x"></i></a> --}}
            <a href="https://web.facebook.com/profile.php?id=100064106499810" target="_blank"><i class="fab fa-facebook fa-3x"></i></a>
            {{-- <a href="https://github.com/bradtraversy" target="_blank"><i class="fab fa-github fa-3x"></i></a> --}}
            <a href="https://www.linkedin.com/company/100603971" target="_blank"><i class="fab fa-linkedin fa-3x"></i></a>
        </div>
    </section>
</body>
</html>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400&display=swap');

:root {
	--primary-color: #3a4052;
}

* {
	box-sizing: border-box;
	margin: 0;
	padding: 0;
}

body {
	font-family: 'Open Sans', sans-serif;
	line-height: 1.5;
}

a {
	text-decoration: none;
	color: var(--primary-color);
}

h3 {
	font-weight: 800;
	font-size: 3rem;
	line-height: 1.2;
	margin-bottom: 15px;
    color: rgb(122, 166, 78);
}
.color-text{
    color: rgb(122, 166, 78);
}

#about {
	padding: 40px;
	text-align: left;
}

#about p {
	font-size: 1.2rem;
	max-width: 600px;
	/* margin: auto; */
}

#about h4 {
	margin: 30px 0;
	color: rgb(122, 166, 78);
}

.social a {
	margin: 0 5px;
}

</style>

