// Load and assign modules 
var id3 = require('id3-writer');
var writer = new id3.Writer();
var fs = require('fs'),
	request = require('request');

 
var videoId = process.argv[2],
	filename = __dirname + '/../YTDownloader/downloads/' + videoId + '.mp3';

fs.stat(filename, function (err, stat) {
	if (err) {
		console.log(err);
	}

	var awesomeUrl = 'https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=' + videoId + '&format=json';
	request(awesomeUrl, function (err, response, body) {
		if (err || response.statusCode != 200) {
			console.log(err);
		}

		var data = JSON.parse(body);
		writeTags(data);
		
	});
});

function writeTags(data) {
	var imageUrl = 'http://img.youtube.com/vi/' + videoId + '/hqdefault.jpg';

	request(imageUrl)
		.pipe(fs.createWriteStream(__dirname + '/tmp/' + videoId + '.jpg'))
		.on('close', function () {
			var file = new id3.File(filename);
			var coverImage = new id3.Image(__dirname + '/tmp/' + videoId + '.jpg');
			var meta = new id3.Meta({
			    artist: data.author_name,
			    title: data.title,
			    album: data.author_name,
			    desc: 'Downloaded from letmusicplay.in',
			    genre: 'letmusicplay.in',
			    comment: 'Downloaded from letmusicplay.in'
			}, [coverImage]);
			 
			writer.setFile(file).write(meta, function (err) {
			 
			    if (err) {
			        console.log(err);
			        return;
			    }
			 
			    console.log('Data was successfully written');
			});
		});
}
