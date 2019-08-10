<?php

namespace OrpheusAppBundle\Utils;

final class ErrorMessage
{
    const RESOURCE_NOT_FOUND = "Resource not found";
    const EMAIL_TAKEN = "Email already taken";
    const PLAYLIST_ALREADY_EXISTS = "Playlist with the same name already exists";
    const PLAYLIST_IMMUTABLE = "Playlist is unalterable";
    const INVALID_DATA = "Submitted data is invalid";
    const INVALID_ARTIST_NAME = "Artist name is invalid";
    const INVALID_PLAYLIST_NAME = "Playlist name is invalid";
    const INVALID_CREDENTIALS = "Invalid credentials";
    const CANNOT_DELETE_ARTIST = "You cannot delete this artist";
    const PASSWORD_TOO_SHORT = "Password has to be at least 6 symbols";
    const SONG_ALREADY_PRESENT = "Song already present in playlist";
}