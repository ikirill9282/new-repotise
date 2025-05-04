$(document).ready(function() {
  let writers = new CommentWriters();
  let editors = new Editors();
  const likers = new LikeButtons('.block_commends');
  const repliers = new RepliesButtons('.commend');
  
  repliers.onAfterDiscover(() => likers.discover('.feed-item'));
});