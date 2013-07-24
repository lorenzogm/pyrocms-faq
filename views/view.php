<div class="row-fluid">
    <div class="span9">
        <h1>{{ entry:title }}</h1>
        <p>{{ entry:description }}</p>
    </div>
    <div class="span3">
        {{ faqs:entries }}
        <p><a href="{{ url:site }}faq/view/{{ id }}">{{ title }}</a></p>
        {{ /faqs:entries }}
    </div>
</div>