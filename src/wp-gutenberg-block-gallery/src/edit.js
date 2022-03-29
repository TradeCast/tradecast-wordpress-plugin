import { compose } from '@wordpress/compose';
import { useBlockProps } from '@wordpress/block-editor';
import { withSelect } from '@wordpress/data';
import { Card, CardBody, CardHeader } from '@wordpress/components';

const Edit = (props) => {
  const {
    attributes: { galleryId, columns },
    entities,
  } = props;

  const blockProps = useBlockProps({});

  /**
   * Returns a loader template.
   */
  if (!entities) {
    return <div {...blockProps}>Loading...</div>;
  }

  /**
   * Handles changes. On change, adds the gallery id to the attributes.
   *
   * @param event
   */
  const onChangeTradecastGalleryId = (event) => {
    props.setAttributes({
      galleryId: Number(event.target.value),
    });
  };

  /**
   * Handles changes. On change, adds the number of columns to the attributes.
   *
   * @param event
   */
  const onChangeTradecastGalleryColumns = (event) => {
    props.setAttributes({
      columns: Number(event.target.value),
    });
  };

  /**
   * Returns the template.
   */
  return (
    <div {...blockProps}>
      <Card>
        <CardHeader>
          <b>Tradecast Gallery</b>
        </CardHeader>
        <CardBody>
          <p>
            <span>Gallery:</span>
            <br />
            <select value={galleryId} onChange={onChangeTradecastGalleryId}>
              <option>&nbsp;</option>
              {entities &&
                entities.map((entities) => {
                  return <option value={entities.id}>{entities.title.rendered}</option>;
                })}
            </select>
          </p>
          <p>
            <span>Columns:</span>
            <br />
            <select value={columns} onChange={onChangeTradecastGalleryColumns}>
              <option value={1}>1 column</option>
              <option value={2}>2 columns</option>
              <option value={3}>3 columns</option>
              <option value={4}>4 columns</option>
            </select>
          </p>
        </CardBody>
      </Card>
    </div>
  );
};

/**
 * Fetches the gallery entities from the WordPress API and feeds them to the Edit method above.
 */
export default compose([
  withSelect((select) => {
    return {
      entities: select('core').getEntityRecords('postType', 'tradecast-galleries', {
        page: 1,
        perPage: 100,
        orderBy: 'date',
        order: 'desc',
      }),
    };
  }),
])(Edit);
