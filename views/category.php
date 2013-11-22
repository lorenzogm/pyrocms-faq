<h1>{{ category:name }}</h1>

<div class="row-fluid" id="faqs">

	<div class="span9">
		{{ faqs:entries }}
		<div class="row-fluid">
			<p><h2>{{ name }}</h2></p>
			<p>{{ description }}</p>
		</div>

		<hr />
		{{ /faqs:entries }}
	</div>

	<div class="span3">
		<p><h2>Preguntas frecuentes</h2></p>
		{{ categories:entries }}
		<p><h3><a href="{{ url:site }}faq/{{ slug }}">{{ name }}</a><h3></p>
		{{ /categories:entries }}
	</div>

</div>