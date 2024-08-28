import dayjs from "dayjs";
const salt = "5876583978";
const Helpers = {
  // Slugify src: https://gist.github.com/codeguy/6684588
  slugify: (text, separator = "-") => {
    return text
      .toString() // Cast to string (optional)
      .normalize("NFKD") // The normalize() using NFKD method returns the Unicode Normalization Form of a given string.
      .replace(/[\u0300-\u036f]/g, "") // remove all previously split accents
      .toLowerCase() // Convert the string to lowercase letters
      .trim() // Remove whitespace from both sides of a string (optional)
      .replace(/\s+/g, separator) // Replace spaces with -
      .replace(/[^\w\-]+/g, "") // Remove all non-word chars
      .replace(/\_/g, separator) // Replace _ with -
      .replace(/\-\-+/g, separator) // Replace multiple - with single -
      .replace(/\-$/g, ""); // Remove trailing -
  },

  filenameValidator: (fname, { replacement = " " } = {}) => {
    // https://stackoverflow.com/a/31976060
    // https://gist.github.com/doctaphred/d01d05291546186941e1b7ddc02034d3

    // resolve multi-line, whitespace trimming
    fname = fname
      .split(/[\r\n]/)
      .map((s) => s.trim())
      .filter((s) => s.length)
      .join(" ");

    // forbidden characters
    // (after multi-line, because new-line-chars are themselves forbidden characters)
    fname = fname.replaceAll(/[<>:"\/\\\|?*\x00-\x1F]/g, replacement);

    // advanced trim
    fname = fname.replace(/\.$/, "");

    // empty filename
    if (!fname.length) {
      fname = "_";
    }

    // forbidden filenames
    if (
      fname.match(
        /^(CON|PRN|AUX|NUL|COM1|COM2|COM3|COM4|COM5|COM6|COM7|COM8|COM9|LPT1|LPT2|LPT3|LPT4|LPT5|LPT6|LPT7|LPT8|LPT9)(\..+)?$/
      )
    ) {
      fname = `_${fname}`;
    }

    return fname;
  },

  // https://stackoverflow.com/a/48593447
  uniqid: (prefix = "", random = false) => {
    const sec = Date.now() * 1000 + Math.random() * 1000;
    const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0");
    return `${prefix}${id}${
      random ? `.${Math.trunc(Math.random() * 100000000)}` : ""
    }`;
  },

  updateModel: (models, updated, addIfNotExists = true, key = "id") => {
    let model = models.find((model) => model[key] == updated[key]);
    if (model) {
      Object.assign(model, updated);
    } else {
      if (addIfNotExists) {
        models.unshift(updated);
      }
    }
  },

  onRequestError: (error, InputFields) => {
    let errors = error.response?.data?.errors;
    for (const key in errors) {
      InputFields[key].setError(errors[key][0]);
    }
  },

  formatDate: (date, fallback = null, format = "MMMM DD, YYYY") => {
    if (!date || (!!date && !dayjs(date).isValid())) return fallback;
    return dayjs(date).format(format);
  },

  formatFileSize: (sizeInBytes) => {
    if (sizeInBytes < 1024) {
      return sizeInBytes + " bytes";
    } else if (sizeInBytes < 1048576) {
      return (sizeInBytes / 1024).toFixed(2) + " KB";
    } else if (sizeInBytes < 1073741824) {
      return (sizeInBytes / 1048576).toFixed(2) + " MB";
    } else {
      return (sizeInBytes / 1073741824).toFixed(2) + " GB";
    }
  },

  shuffleArray: (array) => {
    for (let i = array.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
  },

  CapitalizeFirstLetter: function (str) {
    return str
      ? str
          .toLowerCase()
          .replace(/(^\w{1})|(\s{1}\w{1})/g, (match) => match.toUpperCase())
      : "";
  },

  // https://stackoverflow.com/a/52171480/5063845
  cyrb53: function (str, seed = 0) {
    let h1 = 0xdeadbeef ^ seed,
      h2 = 0x41c6ce57 ^ seed;
    for (let i = 0, ch; i < str.length; i++) {
      ch = str.charCodeAt(i);
      h1 = Math.imul(h1 ^ ch, 2654435761);
      h2 = Math.imul(h2 ^ ch, 1597334677);
    }
    h1 =
      Math.imul(h1 ^ (h1 >>> 16), 2246822507) ^
      Math.imul(h2 ^ (h2 >>> 13), 3266489909);
    h2 =
      Math.imul(h2 ^ (h2 >>> 16), 2246822507) ^
      Math.imul(h1 ^ (h1 >>> 13), 3266489909);
    return 4294967296 * (2097151 & h2) + (h1 >>> 0);
  },

  extractHtmlContent: function (s, space = false) {
    var span = document.createElement("span");
    span.innerHTML = s;
    if (space) {
      var children = span.querySelectorAll("*");
      for (var i = 0; i < children.length; i++) {
        if (children[i].textContent) children[i].textContent += " ";
        else children[i].innerText += " ";
      }
    }
    return [span.textContent || span.innerText].toString().replace(/ +/g, " ");
  },

  passwordGeneratorFn: function (length, option) {
    var defaults = {
      lower: true,
      upper: true,
      number: true,
      symbol: false,
      excludeSimilarCharacters: true,
      exclude: "",
      excludeSimilarCharactersThreshold: 0.2,
      excludeSimilarCharactersExclude: "oO0iIl1",
    };
    var options = Object.assign(defaults, option);
    var exclude = options.exclude;
    var excludeSimilarCharacters = options.excludeSimilarCharacters;
    var excludeSimilarCharactersExclude =
      options.excludeSimilarCharactersExclude;
    var excludeSimilarCharactersThreshold =
      options.excludeSimilarCharactersThreshold;
    var lower = options.lower;
    var upper = options.upper;
    var number = options.number;
    var symbol = options.symbol;
    var chars = "";
    if (lower) {
      chars += "abcdefghijklmnopqrstuvwxyz";
    }
    if (upper) {
      chars += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if (number) {
      chars += "0123456789";
    }
    if (symbol) {
      chars += "!@#$%^&*()_+-=[]{}|;':,./<>?";
    }
    if (exclude) {
      chars = chars.replace(new RegExp(exclude, "g"), "");
    }
    if (excludeSimilarCharacters) {
      var excludeSimilarCharactersChars = "";
      for (let i = 0; i < chars.length; i++) {
        var char = chars[i];
        if (excludeSimilarCharactersExclude.indexOf(char) == -1) {
          excludeSimilarCharactersChars += char;
        }
      }
      var excludeSimilarCharactersCharsLength =
        excludeSimilarCharactersChars.length;
      if (
        excludeSimilarCharactersCharsLength >
        excludeSimilarCharactersThreshold * chars.length
      ) {
        chars = excludeSimilarCharactersChars;
      }
    }
    var password = "";
    for (let i = 0; i < length; i++) {
      var newChar = chars[Math.floor(Math.random() * chars.length)];
      if (newChar) password += newChar;
    }
    return password;
  },

  moneyFormat: function (money) {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "PHP",
    }).format(money);
  },

  dashToHuman: (str) => {
    return !!str
      ? Helpers.CapitalizeFirstLetter(
          str.replaceAll("-", " ").replaceAll("_", " - ")
        )
      : "";
  },

  computeAge: (birthdate) => {
    if (!birthdate) return null;
    const birthDate = dayjs(birthdate);
    const currentDate = dayjs();

    const ageInYears = currentDate.diff(birthDate, "year");
    const ageInMonths = currentDate.diff(birthDate, "month") % 12;
    const ageInDays = currentDate.diff(birthDate, "day") % 30;

    return { years: ageInYears, months: ageInMonths, days: ageInDays };
  },

  crypt: (text) => {
    const textToChars = (text) => text.split("").map((c) => c.charCodeAt(0));
    const byteHex = (n) => ("0" + Number(n).toString(16)).substr(-2);
    const applySaltToChar = (code) =>
      textToChars(salt).reduce((a, b) => a ^ b, code);

    return text
      .split("")
      .map(textToChars)
      .map(applySaltToChar)
      .map(byteHex)
      .join("");
  },

  decrypt: (encoded) => {
    const textToChars = (text) => text.split("").map((c) => c.charCodeAt(0));
    const applySaltToChar = (code) =>
      textToChars(salt).reduce((a, b) => a ^ b, code);
    return encoded
      .match(/.{1,2}/g)
      .map((hex) => parseInt(hex, 16))
      .map(applySaltToChar)
      .map((charCode) => String.fromCharCode(charCode))
      .join("");
  },

  filterObj: (obj) =>
    Object.fromEntries(Object.entries(obj).filter(([_, v]) => v != null)),

  deepCopy: (valu) => JSON.parse(JSON.stringify(valu)),

  brightness: (r, g, b) => {
    let red = r * 299;
    let green = g * 587;
    let blue = b * 114;
    return (red + green + blue) / 1000;
  },

  hex2rgba: (str) => {
    if (!str) return null;
    let num = parseInt(str.slice(1), 16); // Convert to a number
    let r = (num >> 24) & 255,
      g = (num >> 16) & 255,
      b = (num >> 8) & 255,
      a = (num & 255) / 255;
    return { r: r / 255, g: g / 255, b: b / 255, a: a };
  },

  //https://stackoverflow.com/a/27194360
  pluralize: (str, revert = false) => {
    var plural = {
      "(quiz)$": "$1zes",
      "^(ox)$": "$1en",
      "([m|l])ouse$": "$1ice",
      "(matr|vert|ind)ix|ex$": "$1ices",
      "(x|ch|ss|sh)$": "$1es",
      "([^aeiouy]|qu)y$": "$1ies",
      "(hive)$": "$1s",
      "(?:([^f])fe|([lr])f)$": "$1$2ves",
      "(shea|lea|loa|thie)f$": "$1ves",
      sis$: "ses",
      "([ti])um$": "$1a",
      "(tomat|potat|ech|her|vet)o$": "$1oes",
      "(bu)s$": "$1ses",
      "(alias)$": "$1es",
      "(octop)us$": "$1i",
      "(ax|test)is$": "$1es",
      "(us)$": "$1es",
      "([^s]+)$": "$1s",
    };

    var singular = {
      "(quiz)zes$": "$1",
      "(matr)ices$": "$1ix",
      "(vert|ind)ices$": "$1ex",
      "^(ox)en$": "$1",
      "(alias)es$": "$1",
      "(octop|vir)i$": "$1us",
      "(cris|ax|test)es$": "$1is",
      "(shoe)s$": "$1",
      "(o)es$": "$1",
      "(bus)es$": "$1",
      "([m|l])ice$": "$1ouse",
      "(x|ch|ss|sh)es$": "$1",
      "(m)ovies$": "$1ovie",
      "(s)eries$": "$1eries",
      "([^aeiouy]|qu)ies$": "$1y",
      "([lr])ves$": "$1f",
      "(tive)s$": "$1",
      "(hive)s$": "$1",
      "(li|wi|kni)ves$": "$1fe",
      "(shea|loa|lea|thie)ves$": "$1f",
      "(^analy)ses$": "$1sis",
      "((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$":
        "$1$2sis",
      "([ti])a$": "$1um",
      "(n)ews$": "$1ews",
      "(h|bl)ouses$": "$1ouse",
      "(corpse)s$": "$1",
      "(us)es$": "$1",
      s$: "",
    };

    var irregular = {
      move: "moves",
      foot: "feet",
      goose: "geese",
      sex: "sexes",
      child: "children",
      man: "men",
      tooth: "teeth",
      person: "people",
    };

    var uncountable = [
      "sheep",
      "fish",
      "deer",
      "moose",
      "series",
      "species",
      "money",
      "rice",
      "information",
      "equipment",
    ];

    // save some time in the case that singular and plural are the same
    if (uncountable.indexOf(str.toLowerCase()) >= 0) return str;

    // check for irregular forms
    for (let word in irregular) {
      if (revert) {
        var pattern = new RegExp(irregular[word] + "$", "i");
        var replace = word;
      } else {
        var pattern = new RegExp(word + "$", "i");
        var replace = irregular[word];
      }
      if (pattern.test(str)) return str.replace(pattern, replace);
    }

    if (revert) var array = singular;
    else var array = plural;

    // check for matches using regular expressions
    for (let reg in array) {
      var pattern = new RegExp(reg, "i");

      if (pattern.test(str)) return str.replace(pattern, array[reg]);
    }

    return str;
  },

  stripSlashes: (str, leading = true, trailing = true) => {
    if (leading) str = str.replace(/^\/+/, "");
    if (trailing) str = str.replace(/\/+$/, "");
    return str;
  },

  getRandomInt: (min, max) => {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1) + min);
  },
};
export default Helpers;
