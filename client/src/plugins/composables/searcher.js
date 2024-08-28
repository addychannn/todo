import { inject, ref, toValue } from "vue";
import { useRoute, useRouter } from "vue-router";
import { Helpers } from "@/scripts";

const useSearcher = (uri, options = {}) => {
  const $route = useRoute();
  const $router = useRouter();

  const _options = Object.assign(
    {
      method: "get",
      appendToUrl: false,
    },
    toValue(options)
  );

  const _pagination = {
    page: 1,
    pages: 1,
    total: 0,
    ototal: 0,
    limit: 25,
    offset: 0,

    sort: "id",
    order: "asc",
  };

  const $api = inject("$api");

  const getDefaultPagination = () => {
    return Object.assign({}, _pagination, _options.pagination);
  };
  const pagination = ref(getDefaultPagination());

  const searcher = (params = {}) => {
    const _uri = toValue(uri);
    let page = {
      limit: pagination.value.limit,
      order: pagination.value.order,
      orderBy: pagination.value.sort,
    };
    if (pagination.value.offset >= pagination.value.limit) {
      Object.assign(page, {
        page: Math.ceil(pagination.value.offset / pagination.value.limit + 1),
      });
    }
    let _params = Object.assign({}, page, params);
    if (_options.appendToUrl) {
      let filteredObj = formatObjParams(params);
      $router.push({
        name: $route.name,
        query: Object.assign({}, page, filteredObj ?? {}),
      });
    }
    Object.assign(_params, { offset: pagination.value.offset });
    if (_options.method === "get") {
      _params = { params: _params };
    }
    return new Promise((resolve, reject) => {
      $api[_options.method](toValue(_uri), _params)
        .then((response) => {
          if (response.data.count > 0 && response.data.data.length <= 0) {
            pagination.value.page = 1;
            pagination.value.offset = 0;
            searcher(params);
          } else {
            resolve(response);
          }
        })
        .catch((error) => reject(error));
    });
  };

  const resetPagination = () => {
    pagination.value = getDefaultPagination();
  };

  const formatObjParams = (params) => {
    let tmp = null;
    Object.keys(Helpers.filterObj(params)).forEach((key) => {
      if (Object.keys(Helpers.filterObj(params[key])).length > 0) {
        tmp = Object.assign({}, tmp, {
          [key]: JSON.stringify(Helpers.filterObj(params[key])),
        });
      }
    });
    return tmp;
  };

  const limiter = (limit) => {
    return limit > 1000 ? 1000 : limit < 1 ? 1 : limit;
  };

  const readRouteQuery = (search, searchKey = null) => {
    let paginationKeys = ["limit", "offset", "order", "orderBy", "page"];
    Object.keys($route.query)
      .filter((key) => !paginationKeys.includes(key))
      .forEach((key) => {
        let tmp = !!searchKey ? $route.query[searchKey] : $route.query[key];
        tmp = JSON.parse(tmp);
        if (typeof tmp == "object" && !!searchKey) {
          Object.keys(tmp).forEach((item) => {
            search[item] = tmp[item];
          });
        } else {
          search[key] = tmp;
        }
      });
    pagination.value.limit = limiter(
      1 * (parseInt($route.query?.limit) || pagination.value.limit)
    );
    pagination.value.page = parseInt($route.query?.page) || 1;
    pagination.value.offset =
      pagination.value.limit * (pagination.value.page - 1);
    pagination.value.order = $route.query?.order || pagination.value.order;
    pagination.value.sort = $route.query?.orderBy || pagination.value.sort;
  };

  return {
    searcher,
    pagination,
    resetPagination,
    readRouteQuery,
  };
};

export default useSearcher;
