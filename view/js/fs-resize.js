function fs_resize(filename, width, height) {
    var extension = filename.split(".").slice(-1)[0];
    var hashString = filename + "#f" + width + "x" + height + "." + extension;
    var hash = md5(hashString).slice(0, 10);
    return filename + "." + hash + "-f" + width + "x" + height + "." + extension;
}
