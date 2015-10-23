var showFilms = function showfilms(data) {
	var append = "";
	var count = 1;
	for(var i = 0; i < data.rows.length; i++) {
    	if(count % 4 == 0) {
	    	append += "<div class='row'>"
    	}
    	var pre_append = "<div class='col-lg-3'><div class=\"panel panel-default\"><img src=\"imgproxy.php?url=" + data.rows[i].cover.value + " class=\"img-rounded\" alt=\"cover\" style=\"width:80%; margin-left: 10%; margin-top: 25px;\"/><h3 style=\"text-align:center;\">" + data.rows[i].label.value + "<br/><a href=\"showmovie.php?movie=" + data.rows[i].film.value + "\"><button type=\"button\" class=\"btn btn-info\" style=\"margin-top: 10px; margin-bottom: 10px;\">View more information</button></a></h3></div></div>";
    	append += pre_append;
    	if(count % 4 == 0) {
	    	append += "</div>"
    	}
    	count += 1;
	}
	$("#appendto").html(append);
}

var selectedGenre = "";
var original = "";
var showGenre = function showGenre(genre) {
	if(genre == selectedGenre) {
		$("#appendto").html(original);
		$('.btn-warning').removeClass('btn-warning').addClass('btn-default');
		selectedGenre = "";
	} else {
	selectedGenre = genre;
	
		$.getJSON('filmbygenre.php',data={'genre': genre}, function(data){
					
			var append = "";
			console.log(data);
			if(data.results.bindings.length < 1) {
				append += "<h3 style=\"margin-left:25px;\">Unfortunately no recommendations are found for your movie of choice...</h3>";
				$("#appendto").html(append);
				$('.btn-warning').removeClass('btn-warning').addClass('btn-default');
				$("#"+genre.replace("http://dbpedia.org/resource/Category:", "")).removeClass('btn-default').addClass('btn-warning');
			} else {
				var count = 1;
				for(var i = 0; i < data.results.bindings.length; i++) {
			    	if(count % 4 == 0) {
				    	append += "<div class='row'>"
			    	}
			    	var pre_append = "<div class='col-lg-3'><div class=\"panel panel-default\"><img src=\"imgproxy.php?url=" + data.results.bindings[i].cover.value + " class=\"img-rounded\" alt=\"cover\" style=\"width:80%; margin-left: 10%; margin-top: 25px;\"/><h3 style=\"text-align:center;\">" + data.results.bindings[i].label.value + "<br/><a href=\"showmovie.php?movie=" + data.results.bindings[i].film.value + "\"><button type=\"button\" class=\"btn btn-info\" style=\"margin-top: 10px; margin-bottom: 10px;\">View more information</button></a></h3></div></div>";
			    	append += pre_append;
			    	if(count % 4 == 0) {
				    	append += "</div>"
			    	}
			    	count += 1;
				}
				$("#appendto").html(append);
				$('.btn-warning').removeClass('btn-warning').addClass('btn-default');
				$("#"+genre.replace("http://dbpedia.org/resource/Category:", "")).removeClass('btn-default').addClass('btn-warning');
			}
		});
	}
}

var sparqlQuery = function sparqlQuery(query) {
	$.getJSON('sparql.php',data={'query': query, 'reasoning': 1}, function(data){
		showFilms(data);
	});
}


var startCollection = function startCollection() {	
	sparqlQuery('SELECT ?label ?cover ?film WHERE { ?film <http://thomaskamps.nl/onotolgy.owl#hasCover> ?cover . ?film rdfs:label ?label . FILTER (lang(?label) = \'en\') }');
}

var selectedFriends = "";

var selectFilmsFriends = function selectFilmsFriends(subject) {
	$('#all').removeClass('active');
	$('#indirect').removeClass('active');
	$('#direct').removeClass('active');
	
	$('#'+subject).addClass('active');
	
	switch(subject) {
		
		case 'all':
			var query = 'SELECT ?label ?cover ?film WHERE { ?film <http://thomaskamps.nl/onotolgy.owl#hasCover> ?cover . ?film rdfs:label ?label . FILTER (lang(?label) = \'en\') }';
			var genderselect = "";
			selectedFriends = "";
			break;
		
		case 'direct':
			var query = 'SELECT ?label ?cover ?film WHERE { ?film rdf:type <http://thomaskamps.nl/onotolgy.owl#MovieLikedByFriend> . ?film <http://thomaskamps.nl/onotolgy.owl#hasCover> ?cover . ?film rdfs:label ?label . FILTER (lang(?label) = \'en\') }';
			var genderselect = "<ul class=\"nav nav-pills\"><li role=\"presentation\" id=\"male\"><a onclick=\"selectFilmsGender('male')\">Male</a></li><li role=\"presentation\" id=\"female\"><a onclick=\"selectFilmsGender('female')\">Female</a></li><li role=\"presentation\" id=\"allgender\" class=\"active\"><a onclick=\"selectFilmsGender('allgender')\">Both</a></li></ul>";
			selectedFriends = "direct";
			break;
		case 'indirect':
			var query = 'SELECT ?label ?cover ?film WHERE { ?film rdf:type <http://thomaskamps.nl/onotolgy.owl#MovieLikedByIndirectFriend> . ?film <http://thomaskamps.nl/onotolgy.owl#hasCover> ?cover . ?film rdfs:label ?label . FILTER (lang(?label) = \'en\') }';
			var genderselect = "<ul class=\"nav nav-pills\"><li role=\"presentation\" id=\"male\"><a onclick=\"selectFilmsGender('male')\">Male</a></li><li role=\"presentation\" id=\"female\"><a onclick=\"selectFilmsGender('female')\">Female</a></li><li role=\"presentation\" id=\"allgender\" class=\"active\"><a onclick=\"selectFilmsGender('allgender')\">Both</a></li></ul>";
			selectedFriends = "indirect";
			break;	
	} 
	sparqlQuery(query);
	$('#genderselect').html(genderselect)
}

var selectFilmsGender = function selectFilmsGender(gender) {
	$('#male').removeClass('active');
	$('#female').removeClass('active');
	$('#allgender').removeClass('active');
	
	$('#'+gender).addClass('active');
	
	switch(selectedFriends) {
		
		case 'direct':
			switch(gender) {
				case 'male':
					var selectclass = 'MovieLikedByMaleFriend';
					break;
				case 'female':
					var selectclass = 'MovieLikedByFemaleFriend';
					break;
				case 'allgender':
						var selectclass = 'MovieLikedByFriend';
						break;
			}
			break;
			
		case 'indirect':
				switch(gender) {
					case 'male':
						var selectclass = 'MovieLikedByMaleIndirectFriend';
						break;
					case 'female':
						var selectclass = 'MovieLikedByFemaleIndirectFriend';
						break;
					case 'allgender':
						var selectclass = 'MovieLikedByIndirectFriend';
						break;
				}
			break;	
	} 
	var query = 'SELECT ?label ?cover ?film WHERE { ?film rdf:type <http://thomaskamps.nl/onotolgy.owl#' + selectclass + '> . ?film <http://thomaskamps.nl/onotolgy.owl#hasCover> ?cover . ?film rdfs:label ?label . FILTER (lang(?label) = \'en\') }';
	sparqlQuery(query);

} 

var submitSearch = function submitSearch(theForm) {
	
	$.getJSON('dbpediasparql.php',data={'subject': theForm.search.value}, function(data){
		
		if(data.rows.length == 0) {
			
			$('#appendto').html("<h3 style=\"margin-left:25px;\">Your search unfortunately returned no matches, please try again with a different query.");
			
		} else {
			
			$.getJSON('dbpediasparql.php',data={'movie': data.rows[0].c1.value}, function(data){
				
				var genres = "";
				for(var x = 0; x < data.genre.length; x++) {
					var temp = "<a onclick=\"showGenre('" + data.genre[x].genre + "')\"><button type=\"button\" id=\"" + data.genre[x].genre.replace("http://dbpedia.org/resource/Category:", "") + "\" class=\"btn btn-default\" style=\"margin-bottom: 10px; margin-left: 10px;\">" + data.genre[x].label + "</button></a>";
					genres += temp;
				}
				var append1 = "<h3 style=\"margin-left:25px;\">Recommendations from your friends based on " + data.label + ":</h3><p style=\"margin-left:25px;\">Recommendation based on genres (you can select a specific genre): " + genres + "</p>";
				
				var append = ""
				if(data.results.bindings.length < 1) {
					append += "<h3 style=\"margin-left:25px;\">Unfortunately no recommendations are found for your movie of choice...</h3>";
					$("#appendto").html(append);
				} else {
					var count = 1;
					for(var i = 0; i < data.results.bindings.length; i++) {
				    	if(count % 4 == 0) {
					    	append += "<div class='row'>"
				    	}
				    	var pre_append = "<div class='col-lg-3'><div class=\"panel panel-default\"><img src=\"imgproxy.php?url=" + data.results.bindings[i].cover.value + " class=\"img-rounded\" alt=\"cover\" style=\"width:80%; margin-left: 10%; margin-top: 25px;\"/><h3 style=\"text-align:center;\">" + data.results.bindings[i].label.value + "<br/><a href=\"showmovie.php?movie=" + data.results.bindings[i].film.value + "\"><button type=\"button\" class=\"btn btn-info\" style=\"margin-top: 10px; margin-bottom: 10px;\">View more information</button></a></h3></div></div>";
				    	append += pre_append;
				    	if(count % 4 == 0) {
					    	append += "</div>"
				    	}
				    	count += 1;
					}
					$("#appendto1").html(append1);
					original = append;
					$("#appendto").html(append);
				}
			});
		}
	});
}
