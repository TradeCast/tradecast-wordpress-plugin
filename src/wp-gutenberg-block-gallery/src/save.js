import { useBlockProps } from '@wordpress/block-editor';

const Save = (props) => {
  const {
    attributes: { galleryId, columns },
  } = props;

  // add the gallery id and number of columns to the block's properties
  const blockProps = useBlockProps.save({
    id: galleryId ?? '',
    columns: columns ?? '3',
  });

  // return empty if the gallery id is not set
  if (!galleryId || galleryId === '') {
    return '';
  }

  // parse properties
  let properties = '';

  Object.keys(blockProps).forEach((key) => {
    properties += ' ' + (key !== 'className' ? key : 'class') + '="' + blockProps[key] + '"';
  });

  // return the tradecast-gallery shortcode
  return '[tradecast-gallery' + properties + ']';
};

export default Save;
