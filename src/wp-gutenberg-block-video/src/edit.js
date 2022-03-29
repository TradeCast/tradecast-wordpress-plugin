import { compose } from '@wordpress/compose';
import { useBlockProps } from '@wordpress/block-editor';
import { withSelect } from '@wordpress/data';
import { Card, CardBody, CardHeader, CardMedia } from '@wordpress/components';

const Edit = (props) => {
  const {
    attributes: { videoId },
    entities,
    current,
  } = props;

  const blockProps = useBlockProps({});

  /**
   * Returns a loader template.
   */
  if (!entities && !current) {
    return <div {...blockProps}>Loading...</div>;
  }

  /**
   * Handles changes. On change, adds the video id to the attributes.
   * @param event
   */
  const onChangeTradecastVideoId = (event) => {
    props.setAttributes({
      videoId: Number(event.target.value),
    });
  };

  /**
   * Returns the template.
   */
  return (
    <div {...blockProps}>
      <Card>
        <CardHeader>
          <p>Tradecast Video</p>
        </CardHeader>
        <CardBody>
          <select value={videoId} onChange={onChangeTradecastVideoId}>
            <option>&nbsp;</option>
            {entities &&
              entities.map((entities) => {
                return <option value={entities.id}>{entities.title.rendered}</option>;
              })}
          </select>
          {current && (
            <Card>
              <CardMedia>
                <img alt="thumbnail" src={current?.meta?._tradecast_video_thumbnail_url ?? ''} />
              </CardMedia>
              <CardBody>{current?.content?.raw ?? ''}</CardBody>
            </Card>
          )}
        </CardBody>
      </Card>
    </div>
  );
};

/**
 * Fetches the video entities from the WordPress API and feeds them to the Edit method above.
 */
export default compose([
  withSelect((select, ownProps) => {
    return {
      entities: select('core').getEntityRecords('postType', 'tradecast-videos', {
        page: 1,
        perPage: 25,
        orderBy: 'date',
        order: 'desc',
        search: ownProps.attributes.query,
      }),
      current: select('core').getEntityRecord('postType', 'tradecast-videos', ownProps.attributes.videoId),
    };
  }),
])(Edit);
