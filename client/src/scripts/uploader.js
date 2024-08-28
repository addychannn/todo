/**
 * @author Jhyden Jhoe Marx M. Ellana
 */

class Uploader {
  constructor(options) {
    let _options = {
      axios: null,
      url: null,
      headers: {},
      data: {},
      prependData: false,
      sleep: 1500, // Sleep time in ms
      sleepInterval: 15, // Sleep every after x chunks
      microSleep: 200,

      chunkSize: 1024 * 1024 * 1.5,

      //events
      onProgress: null,
      upRate: null,
    };

    this.options = Object.assign(_options, options);

    if (this.options.axios === null) {
      throw "Axios instance missing!";
    }
  }

  async upload(file, name = null, url = null, data = null, onProgress = null) {
    try {
      if (file === null) {
        throw "File missing!";
      }
      if (this.options.url == null && url == null) {
        throw "API Url missing!";
      }
      let _name = !name ? file.name : name;
      let _uid = null;
      let _timestamp = null;
      let _ext = file.name.split(".").pop();
      let _chunks = this._splitFile(file, this.options.chunkSize);
      let _upload = null;

      let _leStamp = new Date().getTime();
      let _leStarted = new Date().getTime();
      let _uploaded = 0;

      for (let i = 0; i < _chunks.length; i++) {
        let injectData =
          this.prependData && i == 0 ? true : i + 1 == _chunks.length;
        _upload = await this._uploadChunk(
          _chunks[i].data,
          _uid,
          _timestamp,
          _name,
          _ext,
          i,
          _chunks.length,
          url,
          data,
          injectData
        );

        if (!!this.options.upRate) {
          let _leSize = _chunks[i].data.size;
          _uploaded += _leSize;
          let ms = new Date().getTime();
          let ellapsed = (ms - _leStamp) / 1000;
          _leStamp = ms;
          // bytes per second
          let bps = _leSize / ellapsed;
          this.options.upRate({
            bps: bps,
            uploaded: _uploaded,
            total: file.size,
            timeRemaining:
              (file.size - _uploaded) /
              (_uploaded / ((ms - _leStarted) / 1000)),
          });
        }

        if (
          this.options.sleepInterval > 0 &&
          [0, 1].indexOf(i) == -1 &&
          i % this.options.sleepInterval == 0
        ) {
          await new Promise((resolve) =>
            setTimeout(resolve, this.options.sleep)
          );
        }
        if (this.options.microSleep > 0) {
          await new Promise((resolve) =>
            setTimeout(resolve, this.options.microSleep)
          );
        }

        if (_upload.status == 200 || _upload.status == 201) {
          _uid = _upload.data.uid;
          _timestamp = _upload.data.timestamp;
          if (this.options.onProgress) {
            this.options.onProgress((i + 1) / _chunks.length);
          }
          if (onProgress) {
            onProgress((i + 1) / _chunks.length);
          }
        }
      }
      return Promise.resolve(_upload);
    } catch (error) {
      return Promise.reject(error);
    }
  }

  async _uploadChunk(
    chunk,
    uid,
    timestamp,
    name,
    ext,
    index,
    total,
    url = null,
    data = null,
    injectData = false
  ) {
    let formData = new FormData();
    formData.append("uid", uid);
    formData.append("timestamp", timestamp);
    formData.append("file", chunk);
    formData.append("name", name);
    formData.append("chunk", index);
    formData.append("ext", ext);
    formData.append("is_last", index == total - 1 ? 1 : 0);
    formData.append("total", total);
    if (injectData) {
      let _data = data ?? this.options.data ?? {};
      for (let i = 0; i < Object.keys(_data).length; i++) {
        formData.append(Object.keys(_data)[i], Object.values(_data)[i] ?? "");
      }
    }
    return await this.options.axios.post(url ?? this.options.url, formData, {
      headers: this.options.headers,
    });
  }

  _splitFile(file, chunkSize) {
    const chunks = [];
    const chunkCount = Math.ceil(file.size / chunkSize);
    for (let i = 0; i < chunkCount; i++) {
      chunks.push({
        index: i,
        data: file.slice(i * chunkSize, (i + 1) * chunkSize),
        done: false,
      });
    }
    return chunks;
  }
}

export default Uploader;
