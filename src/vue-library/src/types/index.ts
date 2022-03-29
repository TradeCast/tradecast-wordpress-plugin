/**
 * Enums
 */
export enum GalleryTypeEnum {
  CATEGORIES = 'categories',
  FEATURED = 'featured',
  LATEST = 'latest',
  INTERESTS = 'interests',
  POPULAR = 'popular',
}

/**
 * Interfaces
 */
export interface CustomWindowInterface extends Window {
  tradecastWpAdminSettings: {
    nonce: string;
    root: string;
  };
}

/**
 * Types
 */
export type GalleryContentType = {
  title: string;
  type: GalleryTypeEnum;
  ids?: number[];
  titles?: string[];
};

export type Settings = {
  [key: string]: any;
};

export type TradecastSettings = Settings & {
  tradecast: {
    api: string;
    channelId: string;
  };
};

export type WordPressSettings = Settings & {
  wordpress: {
    api: string;
    nonce: string;
    settings?: WordPressSettingsType;
  };
};

/**
 * Responses
 */
export type DataResponse<T> = {
  data: T | null;
  pagination?: PaginationResponse;
  success?: boolean;
};

export type PaginationResponse = {
  page: number;
  total_pages: number;
};

/**
 * Tradecast: Requests
 */
export type TradecastRequest = {
  id?: number;
  filter?: {
    [key: string]: any;
  };
  sort?: {
    [key: string]: any;
  };
  page?: number;
  limit?: number;
  thumb?: number;
};

/**
 * Tradecast: Responses
 */
export type TradecastCategoryResponse = {
  category?: TradecastCategoryType | null;
};

export type TradecastCategoryListResponse = {
  categoryList: {
    page: number;
    pageCount: number;
    resultCount: number;
    results: TradecastCategoryType[];
  };
};

export type TradecastInterestResponse = {
  interest?: TradecastInterestType | null;
};

export type TradecastInterestListResponse = {
  interestList: {
    page: number;
    pageCount: number;
    resultCount: number;
    results: TradecastInterestType[];
  };
};

export type TradecastMediaResponse = {
  media?: TradecastMediaType | null;
};

export type TradecastMediaListResponse = {
  mediaList: {
    page: number;
    pageCount: number;
    resultCount: number;
    results: TradecastMediaType[];
  };
};

export type TradecastSettingsResponse = {
  settings: TradecastSettingsType | undefined;
};

/**
 * Tradecast: Types
 */
export type TradecastCategoryType = TradecastMediaListResponse & {
  id: number;
  title: string;
  description: string | null;
  createdAt: Date;
};

export type TradecastChannelInfoType = {
  name: string | null;
  description: string | null;
};

export type TradecastInterestType = TradecastMediaListResponse & {
  id: number;
  name: string;
};

export type TradecastMediaType = {
  id: number;
  title: string;
  description: string;
  duration: number;
  createdAt: Date;
  embedURL: string;
  thumb?: string;
  categories?: TradecastCategoryType[];
};

export type TradecastPaginationType = {
  page?: number;
  limit?: number;
};

export type TradecastSettingsType = {
  channel: TradecastChannelInfoType;
};

/**
 * Wordpress: types
 */
export type WordPressGalleryType = {
  author: {
    id: number;
    name: string;
  };
  content: string;
  date: Date;
  date_gmt: Date;
  id: number;
  tradecast: {
    gallery_type: string | null;
    gallery_ids: string[] | null;
    gallery_titles: string[] | null;
    gallery_is_inaccessible: boolean;
  };
  modified: Date;
  modified_gmt: Date;
  status: string;
  title: string;
};

export type WordPressSettingsType = {
  channelId: string;
  success?: boolean;
};

export type WordPressVideoType = {
  author: {
    id: number;
    name: string;
  };
  content: string;
  date: Date;
  date_gmt: Date;
  id: number;
  tradecast: {
    video_id: number | null;
    video_embed_url: string | null;
    video_thumbnail_url: string | null;
    video_created_at: Date | null;
    video_is_inaccessible: boolean;
  };
  modified: Date;
  modified_gmt: Date;
  status: string;
  title: string;
};
