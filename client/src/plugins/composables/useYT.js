const YT_WATCH_LINK = "https://www.youtube.com/watch?v=";
const YT_EMBED_LINK = "https://www.youtube.com/embed/";

const extractYTId = (url) => {
  let reg =
    /^(https?:\/\/)?((www\.)?(youtube(-nocookie)?|youtube.googleapis)\.com.*(v\/|v=|vi=|vi\/|e\/|embed\/|user\/.*\/u\/\d+\/)|youtu\.be\/)([_0-9a-z-]+)/i;
  return url.match(reg)?.[7];
};

const useYT = (url) => {
  return new Promise(async (resolve, reject) => {
    try {
      let YTID = extractYTId(url);
      let watchLink = `${YT_WATCH_LINK}${YTID}`;
      let extractLink = `http://www.youtube.com/oembed?url=${watchLink}&format=json`;

      const response = await fetch(extractLink);
      const data = await response.json();

      resolve(
        Object.assign({}, data, {
          embed_url: `${YT_EMBED_LINK}${YTID}?feature=oembed`,
          watch_url: `${YT_WATCH_LINK}${YTID}`,
        })
      );
    } catch (e) {
      reject(e);
    }
  });
};
export default useYT;
