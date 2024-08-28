const Path = class {
  extname = (path) => {
    // let ext = path.split(".");
    // if (!!ext[ext.length - 2]) {
    //   return `.${ext[ext.length - 1]}`;
    // }
    // return "";
    var ext = /^.+\.([^.]+)$/.exec(path);
    return ext == null ? "" : `.${ext[1]}`;
  };
};

export default new Path();
