import axios from 'axios';
import { AxiosService } from './axios';
import {
  Settings,
  WordPressSettingsType,
  WordPressVideoType,
  DataResponse,
  WordPressGalleryType,
  GalleryContentType,
  TradecastMediaType,
} from '../types';

export class WordPressApiService {
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
   * Posts a gallery to the WordPress API.
   *
   * @param gallery
   */
  addGallery(gallery: GalleryContentType): Promise<DataResponse<WordPressGalleryType>> {
    return axios
      .post(this.settings.wordpress.api + '/tradecast/v1/gallery', gallery)
      .then((response) => response.data as DataResponse<WordPressGalleryType>);
  }

  /**
   * Gets a list of galleries from the WordPress API.
   */
  listGalleries(): Promise<DataResponse<WordPressGalleryType[]>> {
    return axios
      .get(this.settings.wordpress.api + '/tradecast/v1/galleries')
      .then((response) => response.data as DataResponse<WordPressGalleryType[]>);
  }

  /**
   * Gets a gallery from the WordPress API.
   *
   * @param id
   */
  getGallery(id: number): Promise<WordPressGalleryType> {
    return axios
      .get(this.settings.wordpress.api + '/tradecast/v1/gallery/' + id)
      .then((response) => response.data as WordPressGalleryType);
  }

  /**
   * Deletes a gallery from the WordPress API.
   *
   * @param id
   */
  deleteGallery(id: number): Promise<WordPressGalleryType | false> {
    return axios
      .delete(this.settings.wordpress.api + '/tradecast/v1/gallery/' + id)
      .then((response) => response.data as WordPressGalleryType | false);
  }

  /**
   * Posts a video to the WordPress API.
   *
   * @param media
   */
  addVideo(media: TradecastMediaType): Promise<DataResponse<WordPressVideoType>> {
    return axios
      .post(this.settings.wordpress.api + '/tradecast/v1/video', media)
      .then((response) => response.data as DataResponse<WordPressVideoType>);
  }

  /**
   * Gets a list of videos from the WordPress API.
   */
  listVideos(): Promise<DataResponse<WordPressVideoType[]>> {
    return axios
      .get(this.settings.wordpress.api + '/tradecast/v1/videos')
      .then((response) => response.data as DataResponse<WordPressVideoType[]>);
  }

  /**
   * Gets a video from the WordPress API.
   *
   * @param id
   */
  getVideo(id: number): Promise<WordPressVideoType> {
    return axios
      .get(this.settings.wordpress.api + '/tradecast/v1/video/' + id)
      .then((response) => response.data as WordPressVideoType);
  }

  /**
   * Deletes a video from the WordPress API.
   *
   * @param id
   */
  deleteVideo(id: number): Promise<WordPressVideoType | false> {
    return axios
      .delete(this.settings.wordpress.api + '/tradecast/v1/video/' + id)
      .then((response) => response.data as WordPressVideoType | false);
  }

  /**
   * Gets settings from the WordPress API.
   *
   * @param refresh
   */
  getSettings(refresh = false): Promise<WordPressSettingsType> {
    if (!refresh && this.settings.wordpress.settings !== undefined) {
      return new Promise<WordPressSettingsType>((resolve) => {
        resolve(this.settings.tradecast as WordPressSettingsType);
      });
    }

    return axios
      .get(this.settings.wordpress.api + '/tradecast/v1/settings')
      .then((response) => response.data as WordPressSettingsType)
      .then((response) => {
        this.settings.wordpress.settings = response;
        return response;
      });
  }

  /**
   * Posts settings to the WordPress API.
   *
   * @param channelId
   */
  setSettings(channelId: string): Promise<WordPressSettingsType> {
    return axios
      .post(this.settings.wordpress.api + '/tradecast/v1/settings', {
        channelId: channelId,
      })
      .then((response) => response.data as WordPressSettingsType)
      .then((response) => {
        this.settings.wordpress.settings = response;
        return response;
      });
  }
}
