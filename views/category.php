<h1>{{ category:name }}</h1>

<div class="row" id="faqs">

	<div class="col-md-12">
		{{ faqs:entries }}
		<div class="row">
			<p><h2>{{ name }}</h2></p>
			<p>{{ description }}</p>
		</div>

		<hr />
		{{ /faqs:entries }}
	</div>

	<div class="col-md-3 text-left hidden">
		<p><h2>Preguntas frecuentes</h2></p>
		{{ categories:entries }}
		<p><h3><a href="{{ url:site }}faq/{{ slug }}">{{ name }}</a><h3></p>
		{{ /categories:entries }}
	</div>

</div>
