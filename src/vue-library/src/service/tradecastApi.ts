import axios from 'axios';
import { AxiosService } from './axios';
import {
  TradecastRequest,
  Settings,
  TradecastCategoryResponse,
  DataResponse,
  TradecastCategoryListResponse,
  TradecastInterestResponse,
  TradecastInterestListResponse,
  TradecastMediaResponse,
  TradecastMediaListResponse,
  TradecastSettingsResponse,
} from '../types';

export class TradecastApiService {
  private settings: Settings;

  /**
   * Initializes this service.
   *
   * @param settings
   */
  constructor(settings: Settings) {
    this.settings = settings;

    AxiosService.init(settings);
  }

  /**
   * Fetches a category from Tradecast's API.
   *
   * @param request
   */
  public getCategory = (request: TradecastRequest): Promise<DataResponse<TradecastCategoryResponse>> => {
    return axios
      .post(
        this.settings.tradecast.api,
        {
          query: `
            query category {
              category(id: ${request.id}) {
                id,
                title,
                mediaList(${this.parseParameters(request)}) {
                  page,
                  pageCount,
                  resultCount,
                  results {
                    id,
                    title,
                    description,
                    duration,
                    createdAt,
                    embedURL,
                    thumb(width: ${request.thumb ?? 360}),
                    categories {
                      id,
                      title
                    }
                  }
                }
              }
            }
          `,
        },
        {
          headers: {
            channelId: this.settings.tradecast.channelId,
          },
        }
      )
      .then((response) => response.data as DataResponse<TradecastCategoryResponse>);
  };

  /**
   * Fetches a category list from Tradecast's API.
   *
   * @param request
   */
  public getCategoryList = (request: TradecastRequest): Promise<DataResponse<TradecastCategoryListResponse>> => {
    return axios
      .post(this.settings.tradecast.api, {
        query: `
            query categories {
              categoryList(${this.parseParameters(request)}) {
                page,
                pageCount,
                resultCount,
                results {
                  id,
                  title,
                  mediaList(filter: {}) {
                    page,
                    pageCount,
                    resultCount,
                    results {
                      id,
                      title,
                      description,
                      duration,
                      createdAt,
                      embedURL,
                      thumb(width: ${request.thumb ?? 360})
                    }
                  }
                }
              }
            }
          `,
      })
      .then((response) => response.data as DataResponse<TradecastCategoryListResponse>);
  };

  /**
   * Fetches an interest from Tradecast's API.
   *
   * @param request
   */
  public getInterest = (request: TradecastRequest): Promise<DataResponse<TradecastInterestResponse>> => {
    return axios
      .post(this.settings.tradecast.api, {
        query: `
            query interest {
              interest(id: ${request.id}) {
                id,
                name,
                mediaList(${this.parseParameters(request)}) {
                  page,
                  pageCount,
                  resultCount,
                  results {
                    id,
                    title,
                    description,
                    duration,
                    createdAt,
                    embedURL,
                    thumb(width: ${request.thumb ?? 360})
                  }
                }
              }
            }
          `,
      })
      .then((response) => response.data as DataResponse<TradecastInterestResponse>);
  };

  /**
   * Fetches an interest list from Tradecast's API.
   *
   * @param request
   */
  public getInterestList = (request: TradecastRequest): Promise<DataResponse<TradecastInterestListResponse>> => {
    return axios
      .post(this.settings.tradecast.api, {
        query: `
            query interests {
              interestList(${this.parseParameters(request)}) {
                page,
                pageCount,
                resultCount,
                results {
                  id,
                  name,
                  mediaList(filter: {}) {
                    page,
                    pageCount,
                    resultCount,
                    results {
                      id,
                      title,
                      description,
                      duration,
                      createdAt,
                      embedURL,
                      thumb(width: ${request.thumb ?? 360})
                    }
                  }
                }
              }
            }
          `,
      })
      .then((response) => response.data as DataResponse<TradecastInterestListResponse>);
  };

  /**
   * Fetches a media item from Tradecast's API.
   *
   * @param request
   */
  public getMedia = (request: TradecastRequest): Promise<DataResponse<TradecastMediaResponse>> => {
    return axios
      .post(this.settings.tradecast.api, {
        query: `
            query media {
              media(id: ${request.id}) {
                id,
                title,
                description,
                duration,
                createdAt,
                embedURL,
                thumb(width: ${request.thumb ?? 360}),
                categories {
                  id,
                  title
                }
              }
            }
          `,
      })
      .then((response) => response.data as DataResponse<TradecastMediaResponse>);
  };

  /**
   * Fetches a media list from Tradecast's API.
   *
   * @param request
   */
  public getMediaList = (request: TradecastRequest): Promise<DataResponse<TradecastMediaListResponse>> => {
    return axios
      .post(this.settings.tradecast.api, {
        query: `
            query mediaList {
              mediaList(${this.parseParameters(request)}) {
                page,
                pageCount,
                resultCount,
                results {
                  id,
                  title,
                  description,
                  duration,
                  createdAt,
                  embedURL,
                  thumb(width: ${request.thumb ?? 360}),
                  categories {
                    id,
                    title,
                  }
                }
              }
            }
          `,
      })
      .then((response) => response.data as DataResponse<TradecastMediaListResponse>);
  };

  /**
   * Fetches channel info from Tradecast's API.
   *
   * @param channelId
   */
  public getChannelInfo = (channelId: string): Promise<DataResponse<TradecastSettingsResponse>> => {
    this.settings.tradecast.channelId = channelId;

    return axios
      .post(this.settings.tradecast.api, {
        query: `
            query settings {
              settings {
                channel {
                  name,
                  description
                }
              }
            }
          `,
      })
      .then((response) => response.data as DataResponse<TradecastSettingsResponse>);
  };

  /**
   * Parses parameters for the request. Adding filters, sorting, page and limit.
   *
   * @param request
   */
  public parseParameters = (request: TradecastRequest): string => {
    let parameters =
      `filter: ` + (request.filter ? `${JSON.stringify(request.filter).replace(/"([^"]+)":/g, '$1:')}` : `{}`);

    if (request.sort) {
      parameters += `, sort: ` + `${JSON.stringify(request.sort).replace(/"([^"]+)"/g, '$1')}`;
    }

    if (request.page) {
      parameters += `, page: ${request.page}`;
    }

    if (request.limit) {
      parameters += `, limit: ${request.limit}`;
    }

    return parameters;
  };
}
