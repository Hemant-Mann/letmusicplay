# API

To view the JSON Data an extra header must be sent with every request
```
X-JSON-Api: SwiftMVC
```

### Most Downloaded
```
/home.json
```
The results will be in 'songs' key

### Search
query: Required (What is to be searched)
```
/music/search.json?q={query}
```
The results will be in 'songs' key containing an array of object with each object image, title, and id of the song.

### Song Info
```
title: Title of the song
id: ID of the song
```

Both title and id can be found after searching
```
/music/view/{title}/{id}.json
```

The result will contain the different formats and extensions in which the video can be downloaded.

### Download
```
/music/download/{quality}/{id}?title={title}&ext={ext}
```
- quality: int | "mp3"
- id: ID of the song
- title: Title of the song
- ext: Extension of the video (mp4 | webm | mp3)

```
quality, id, title, ext can be found from /music/view page
```

