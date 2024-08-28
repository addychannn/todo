import mimeDB from "mime-db";
import path from "./path";

const MimeTypes = class {
  /**
   * Module letiables.
   * @private
   */

  constructor(db, extname) {
    this.db = db;
    this.setExtNameFn(extname);

    this.EXTRACT_TYPE_REGEXP = /^\s*([^;\s]*)(?:;|\s|$)/;
    this.TEXT_TYPE_REGEXP = /^text\//i;

    this.types = Object.create(null);
    this.extensions = Object.create(null);
    this.typeSets = Object.create(null);
    this.charsets = { lookup: this.charset };
    this.populateMaps();
  }

  /**
   * Set extname function for extracting extension from file path/name
   *
   * @param {Function} extname
   */
  setExtNameFn = (extname) => {
    this.extname = extname;
  };

  /**
   * Get the default charset for a MIME type.
   *
   * @param {string} type
   * @return {boolean|string}
   */

  charset = (type) => {
    if (!type || typeof type !== "string") {
      return false;
    }

    // TODO: use media-typer
    let match = this.EXTRACT_TYPE_REGEXP.exec(type);
    let mime = match && this.db[match[1].toLowerCase()];

    if (mime && mime.charset) {
      return mime.charset;
    }

    // default text/* to utf-8
    if (match && this.TEXT_TYPE_REGEXP.test(match[1])) {
      return "UTF-8";
    }

    return false;
  };

  /**
   * Create a full Content-Type header given a MIME type or extension.
   *
   * @param {string} str
   * @return {boolean|string}
   */

  contentType = (str) => {
    // TODO: should this even be in this module?
    if (!str || typeof str !== "string") {
      return false;
    }

    let mime = str.indexOf("/") === -1 ? this.lookup(str) : str;

    if (!mime) {
      return false;
    }

    // TODO: use content-type or other module
    if (mime.indexOf("charset") === -1) {
      let charset = this.charset(mime);
      if (charset) mime += "; charset=" + charset.toLowerCase();
    }

    return mime;
  };

  /**
   * Get the default extension for a MIME type.
   *
   * @param {string} type
   * @return {boolean|string}
   */

  extension = (type) => {
    if (!type || typeof type !== "string") {
      return false;
    }

    // TODO: use media-typer
    let match = this.EXTRACT_TYPE_REGEXP.exec(type);

    // get extensions
    let exts = match && this.extensions[match[1].toLowerCase()];

    if (!exts || !exts.length) {
      return false;
    }

    return exts[0];
  };
  /**
   * Obtain the extension of a filename or filepath.
   * If the path is not a string or a proper extension isn't found,
   *   false is returned.
   * The path is case insensitive (so hello.html and HELLO.HTML are equal).
   *
   * @param {string} path
   * @return {boolean|string} the file extension if available. false otherwise.
   */

  extractExtension = (path) => {
    if (!path || typeof path !== "string") {
      return false;
    }

    // get the extension ("ext" or ".ext" or full path)
    var extension = this.extname("x." + path)
      .toLowerCase()
      .substr(1);

    if (!extension) {
      return false;
    }

    return extension;
  };

  /**
   * Lookup the MIME type for a file path/extension.
   *
   * @param {string} path
   * @return {boolean|string}
   */

  lookup = (path) => {
    if (!path || typeof path !== "string") {
      return false;
    }

    // get the extension ("ext" or ".ext" or full path)
    let extension = this.extname("x." + path)
      .toLowerCase()
      .substr(1);

    if (!extension) {
      return false;
    }

    return this.types[extension] || false;
  };
  /**
   * Find all MIME types that are associated with a file extensions.
   *
   * @param {string} path or file extension
   * @return {boolean|array<string>}
   */

  lookupAll = (path) => {
    var extension = extractExtension(path);

    if (!extension) {
      return false;
    }

    return typeSets[extension] || [];
  };

  /**
   * Populate the extensions and types maps.
   * @private
   */

  populateMaps = () => {
    // source preference (least -> most)
    let preference = ["nginx", "apache", undefined, "iana"];

    let db = this.db;
    let types = this.types;
    let extensions = this.extensions;
    let typeSets = this.typeSets;

    Object.keys(db).forEach(function forEachMimeType(type) {
      let mime = db[type];
      let exts = mime.extensions;

      if (!exts || !exts.length) {
        return;
      }

      // mime -> extensions
      extensions[type] = exts;

      // extension -> mime
      for (let i = 0; i < exts.length; i++) {
        let extension = exts[i];
        if (typeSets[exts[i]]) {
          typeSets[exts[i]].push(type);
        } else {
          typeSets[exts[i]] = [type];
        }

        if (types[extension]) {
          let from = preference.indexOf(db[types[extension]].source);
          let to = preference.indexOf(mime.source);

          if (
            types[extension] !== "application/octet-stream" &&
            (from > to ||
              (from === to &&
                types[extension].substr(0, 12) === "application/"))
          ) {
            // skip the remapping
            continue;
          }
        }

        // set the extension -> mime
        types[extension] = type;
      }
    });
  };
};

export default new MimeTypes(mimeDB, path.extname);
