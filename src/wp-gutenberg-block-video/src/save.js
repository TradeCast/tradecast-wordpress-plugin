import { useBlockProps } from '@wordpress/block-editor';

const Save = (props) => {
  const {
    attributes: { videoId },
  } = props;

  // add the gallery id and number of columns to the block's properties
  const blockProps = useBlockProps.save({
    id: videoId ?? '',
    width: 960,
    height: 540,
    autoplay: false,
  });

  // return empty if the video id is not set
  if (!videoId || videoId === '') {
    return '';
  }

  // parse the properties
  let properties = '';

  Object.keys(blockProps).forEach((key) => {
    properties += ' ' + (key !== 'className' ? key : 'class') + '="' + blockProps[key] + '"';
  });

  // return the tradecast-video shortcode
  return '[tradecast-video' + properties + ']';
};

export default Save;
