SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


--
-- Database: `mytube`
--
DROP DATABASE IF EXISTS `mytube`;
CREATE DATABASE `mytube` DEFAULT CHARACTER SET utf8;
USE `mytube`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Film & Animation'),
(2, 'Autos & Vehicles'),
(3, 'Music'),
(4, 'Pets & Animals'),
(5, 'Sports'),
(6, 'Travel & Events'),
(7, 'Gaming'),
(8, 'People & Blogs'),
(9, 'Comedy'),
(10, 'Entertainment'),
(11, 'News & Politics'),
(12, 'Howto & Style'),
(13, 'Education'),
(14, 'Science & Technology'),
(15, 'Nonprofits & Activism');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `signUpDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `profilePic` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `username`, `email`, `password`, `signUpDate`, `profilePic`) VALUES
('3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'Reece', 'Kenney', 'reece-kenney', 'Reece@gmail.com', '3f9821f642f5ae6b27958fda24d9557d406144865192b102701533fe3778186f87a2aa7a2a5699356cbb0cb772af93fe682d19356c5f808c0515524a5de62c40', '2018-09-30 11:35:09', 'assets/images/profilePictures/default-male.png'),
('6a75e736-06b1-11eb-a500-0e4b956b9dd1', 'Donkey', 'Kong', 'donkey-kong', 'dk@gmail.com', '3f9821f642f5ae6b27958fda24d9557d406144865192b102701533fe3778186f87a2aa7a2a5699356cbb0cb772af93fe682d19356c5f808c0515524a5de62c40', '2018-09-30 11:50:41', 'assets/images/profilePictures/default.png'),
('79d14338-06b1-11eb-a500-0e4b956b9dd1', 'Super', 'Mario', 'mario-sunshine', 'mario@gmail.com', '3f9821f642f5ae6b27958fda24d9557d406144865192b102701533fe3778186f87a2aa7a2a5699356cbb0cb772af93fe682d19356c5f808c0515524a5de62c40', '2018-10-05 19:57:05', 'assets/images/profilePictures/default-female.png'),
('86efb5a4-06b1-11eb-a500-0e4b956b9dd1', 'Mike', 'Wazowski', 'mike123', 'mk@gmail.com', '3f9821f642f5ae6b27958fda24d9557d406144865192b102701533fe3778186f87a2aa7a2a5699356cbb0cb772af93fe682d19356c5f808c0515524a5de62c40', '2018-10-07 17:52:49', 'assets/images/profilePictures/default.png'),
('a02d0970-06b1-11eb-a500-0e4b956b9dd1', 'Little', 'Mac', 'littlemac', 'mac@gmail.com', '3f9821f642f5ae6b27958fda24d9557d406144865192b102701533fe3778186f87a2aa7a2a5699356cbb0cb772af93fe682d19356c5f808c0515524a5de62c40', '2018-10-07 17:52:49', 'assets/images/profilePictures/default-male.png'),
('a02d0c88-06b1-11eb-a500-0e4b956b9dd1', 'Mickey', 'Mouse', 'mickey-mouse', 'mouse@gmail.com', '3f9821f642f5ae6b27958fda24d9557d406144865192b102701533fe3778186f87a2aa7a2a5699356cbb0cb772af93fe682d19356c5f808c0515524a5de62c40', '2018-10-07 17:52:49', 'assets/images/profilePictures/default.png'),
('a02d0cac-06b1-11eb-a500-0e4b956b9dd1', 'Bugs', 'Bunny', 'bugsbunny', 'bugs@gmail.com', '3f9821f642f5ae6b27958fda24d9557d406144865192b102701533fe3778186f87a2aa7a2a5699356cbb0cb772af93fe682d19356c5f808c0515524a5de62c40', '2018-10-07 17:52:49', 'assets/images/profilePictures/default.png'),
('1935f7b3-0706-11eb-a942-0242ac110002', 'Andrey', 'Pavlov', 'andrey-pavlov', 'andrew.m.pavlov@gmail.com', '3f9821f642f5ae6b27958fda24d9557d406144865192b102701533fe3778186f87a2aa7a2a5699356cbb0cb772af93fe682d19356c5f808c0515524a5de62c40', '2020-09-01 15:00:00', 'assets/images/profilePictures/default-male.png');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` char(36) NOT NULL,
  `userTo` char(36) NOT NULL,
  `userFrom` char(36) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_subscribers_user_to` FOREIGN KEY (`userTo`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_subscribers_user_from` FOREIGN KEY (`userFrom`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`id`, `userTo`, `userFrom`) VALUES
('d37f4263-06b2-11eb-a500-0e4b956b9dd1', '6a75e736-06b1-11eb-a500-0e4b956b9dd1', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1'),
('d37f4575-06b2-11eb-a500-0e4b956b9dd1', 'a02d0970-06b1-11eb-a500-0e4b956b9dd1', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1'),
('46108923-06b3-11eb-a500-0e4b956b9dd1', 'a02d0970-06b1-11eb-a500-0e4b956b9dd1', '1935f7b3-0706-11eb-a942-0242ac110002'),
('1bcd6623-06b4-11eb-a500-0e4b956b9dd1', '79d14338-06b1-11eb-a500-0e4b956b9dd1', '1935f7b3-0706-11eb-a942-0242ac110002');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` char(36) NOT NULL,
  `uploadedBy` char(36) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `privacy` int(11) NOT NULL DEFAULT 0,
  `filePath` varchar(255) NOT NULL,
  `category` int(11) NOT NULL DEFAULT 1,
  `uploadDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `views` int(11) NOT NULL DEFAULT 0,
  `duration` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_videos_user` FOREIGN KEY (`uploadedBy`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_videos_category` FOREIGN KEY (`category`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `uploadedBy`, `title`, `description`, `privacy`, `filePath`, `category`, `uploadDate`, `views`, `duration`) VALUES
('46108923-06b3-11eb-a500-0e4b956b9dd1', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'This is a car video', 'Here is a video of my car', 1, 'api/uploads/videos/5bb0ba5665d24.mp4', 2, '2018-09-30 13:58:14', 96, '00:08'),
('a1c7565e-06b3-11eb-a500-0e4b956b9dd1', '79d14338-06b1-11eb-a500-0e4b956b9dd1', 'Dog plays in the sand on the beach', 'This is a video of my dog playing in the sand. He\'s awesome!', 1, 'api/uploads/videos/5bb7a68bd6276.mp4', 4, '2018-10-05 19:59:39', 95, '00:05'),
('be0d25cd-06b3-11eb-a500-0e4b956b9dd1', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'Man playing guitar having fun', 'Some guy playing the guitar', 1, 'api/uploads/videos/5bba23f8e2f8c.mp4', 4, '2018-10-07 17:19:20', 6, '00:10'),
('cb85fe68-06b3-11eb-a500-0e4b956b9dd1', '6a75e736-06b1-11eb-a500-0e4b956b9dd1', 'Woman in front of the computer', '', 1, 'api/uploads/videos/5bba243098e18.mp4', 14, '2018-10-07 17:20:16', 0, '00:06'),
('d7932832-06b3-11eb-a500-0e4b956b9dd1', '6a75e736-06b1-11eb-a500-0e4b956b9dd1', 'Woman walking with phone', 'This is some stock footage I found', 1, 'api/uploads/videos/5bba245a573a3.mp4', 8, '2018-10-07 17:20:58', 7, '00:04'),
('eb462b4a-06b3-11eb-a500-0e4b956b9dd1', '86efb5a4-06b1-11eb-a500-0e4b956b9dd1', 'Ducks! Awesome!', 'Here are some ducks. Enjoy!', 1, 'api/uploads/videos/5bba247a83798.mp4', 4, '2018-10-07 17:21:30', 0, '00:05'),
('f6d7312e-06b3-11eb-a500-0e4b956b9dd1', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'Table tennis with my friends', 'My friends an I playing tennis', 1, 'api/uploads/videos/5bba2c77ae7d8.mp4', 5, '2018-10-07 17:55:35', 1, '00:09'),
('0693c292-06b4-11eb-a500-0e4b956b9dd1', 'a02d0c88-06b1-11eb-a500-0e4b956b9dd1', 'Card peeking', 'Playing poker', 1, 'api/uploads/videos/5bba2ca8896ee.mp4', 7, '2018-10-07 17:56:24', 4, '00:07'),
('11d6e46e-06b4-11eb-a500-0e4b956b9dd1', 'a02d0c88-06b1-11eb-a500-0e4b956b9dd1', 'Kid playing ice hockey', 'He\'s awsome', 1, 'api/uploads/videos/5bba2cd313ebd.mp4', 5, '2018-10-07 17:57:07', 3, '00:13'),
('1bcd6623-06b4-11eb-a500-0e4b956b9dd1', 'a02d0970-06b1-11eb-a500-0e4b956b9dd1', 'Clown fish ', 'Here is a video of a clown fish in water', 1, 'api/uploads/videos/5bba2d55ee97e.mp4', 4, '2018-10-07 17:59:17', 2, '00:05'),
('28d467a9-06b4-11eb-a500-0e4b956b9dd1', 'a02d0c88-06b1-11eb-a500-0e4b956b9dd1', 'Some funny man swimming', '', 1, 'api/uploads/videos/5bba2d7a09460.mp4', 15, '2018-10-07 17:59:54', 1, '00:09');

-- --------------------------------------------------------

--
-- Table structure for table `thumbnails`
--

CREATE TABLE `thumbnails` (
  `id` char(36) NOT NULL,
  `videoId` char(36) NOT NULL,
  `filePath` varchar(255) NOT NULL,
  `selected` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_thumbnails_video` FOREIGN KEY (`videoId`) REFERENCES `videos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `thumbnails`
--

INSERT INTO `thumbnails` (`id`, `videoId`, `filePath`, `selected`) VALUES
('25', '46108923-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/154-5bb0ba5aa936c.jpg', 1),
('26', '46108923-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/154-5bb0ba5ad39b8.jpg', 0),
('27', '46108923-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/154-5bb0ba5b1b7a3.jpg', 0),
('28', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/155-5bb7a68e7dccb.jpg', 1),
('29', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/155-5bb7a68e9fa5f.jpg', 0),
('30', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/155-5bb7a68ecb38c.jpg', 0),
('31', 'be0d25cd-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/156-5bba23fbd942d.jpg', 0),
('32', 'be0d25cd-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/156-5bba23fc048bd.jpg', 0),
('33', 'be0d25cd-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/156-5bba23fc30d48.jpg', 1),
('34', 'cb85fe68-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/157-5bba24337107c.jpg', 1),
('35', 'cb85fe68-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/157-5bba243393bec.jpg', 0),
('36', 'cb85fe68-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/157-5bba2433c32f8.jpg', 0),
('37', 'd7932832-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/158-5bba245c7fee8.jpg', 1),
('38', 'd7932832-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/158-5bba245c9c372.jpg', 0),
('39', 'd7932832-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/158-5bba245cbf323.jpg', 0),
('40', 'eb462b4a-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/159-5bba247ebfcc4.jpg', 1),
('41', 'eb462b4a-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/159-5bba247ee4ad9.jpg', 0),
('42', 'eb462b4a-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/159-5bba247f22bfa.jpg', 0),
('43', 'f6d7312e-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/160-5bba2c7c483e6.jpg', 1),
('44', 'f6d7312e-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/160-5bba2c7c77d22.jpg', 0),
('45', 'f6d7312e-06b3-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/160-5bba2c7cbc94b.jpg', 0),
('46', '0693c292-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/161-5bba2cb995c1d.jpg', 1),
('47', '0693c292-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/161-5bba2cba21a63.jpg', 0),
('48', '0693c292-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/161-5bba2cbae6700.jpg', 0),
('49', '11d6e46e-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/162-5bba2cdb87604.jpg', 1),
('50', '11d6e46e-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/162-5bba2cdbc068a.jpg', 0),
('51', '11d6e46e-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/162-5bba2cdc29934.jpg', 0),
('52', '1bcd6623-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/163-5bba2d59ae676.jpg', 1),
('53', '1bcd6623-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/163-5bba2d59d15fb.jpg', 0),
('54', '1bcd6623-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/163-5bba2d5a0cd2b.jpg', 0),
('55', '28d467a9-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/164-5bba2d7cb0414.jpg', 1),
('56', '28d467a9-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/164-5bba2d7ccc263.jpg', 0),
('57', '28d467a9-06b4-11eb-a500-0e4b956b9dd1', 'api/uploads/videos/thumbnails/164-5bba2d7cf3ce6.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` char(36) NOT NULL,
  `postedBy` char(36) NOT NULL,
  `videoId` char(36) NOT NULL,
  `responseTo` char(36) DEFAULT NULL,
  `body` text NOT NULL,
  `datePosted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`postedBy`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_comments_video` FOREIGN KEY (`videoId`) REFERENCES `videos` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_comments_comment` FOREIGN KEY (`responseTo`) REFERENCES `comments` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `postedBy`, `videoId`, `responseTo`, `body`, `datePosted`) VALUES
('1', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'Hi everyone! ', '2018-10-06 14:25:14'),
('2', '1935f7b3-0706-11eb-a942-0242ac110002', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'Nice video!', '2018-10-06 14:42:27'),
('3', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'test comment', '2018-10-06 15:09:34'),
('4', 'a02d0cac-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'Cool!', '2018-10-06 15:12:27'),
('5', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'be0d25cd-06b3-11eb-a500-0e4b956b9dd1', NULL, 'Hi guys!', '2018-10-06 15:13:22'),
('6', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'This is a dog', '2018-10-06 15:14:18'),
('7', 'a02d0c88-06b1-11eb-a500-0e4b956b9dd1', 'be0d25cd-06b3-11eb-a500-0e4b956b9dd1', NULL, 'fsfsdsd', '2018-10-06 15:17:12'),
('8', 'a02d0c88-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'Lorem ipsum', '2018-10-06 15:19:05'),
('9', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'hgvjkbj', '2018-10-06 15:30:55'),
('10', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'test', '2018-10-06 15:32:06'),
('11', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'Test comment', '2018-10-06 15:35:42'),
('12', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'fdsasdfsdf', '2018-10-06 16:03:42'),
('13', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'fsdfadsfsdf', '2018-10-06 16:43:47'),
('14', 'a02d0970-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'Hi!', '2018-10-06 16:45:26'),
('15', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'eb462b4a-06b3-11eb-a500-0e4b956b9dd1', NULL, 'fdsfsdfd sdf ', '2018-10-06 16:46:05'),
('16', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'dfsfasdf asdf ', '2018-10-06 16:56:15'),
('17', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'fsdfsdfsdsdf asdf ', '2018-10-06 17:07:12'),
('18', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'asfasdfas asdf ', '2018-10-06 17:07:33'),
('19', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'Hello', '2018-10-06 17:08:05'),
('20', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'fdsafasdf', '2018-10-06 17:24:44'),
('21', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'fgdgdsfgfdsg  fgsd', '2018-10-07 11:12:40'),
('22', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'fsdfd', '2018-10-07 11:20:01'),
('23', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'cb85fe68-06b3-11eb-a500-0e4b956b9dd1', NULL, 'dsfsdf', '2018-10-07 11:21:01'),
('24', '86efb5a4-06b1-11eb-a500-0e4b956b9dd1', 'eb462b4a-06b3-11eb-a500-0e4b956b9dd1', NULL, 'gdgdf', '2018-10-07 11:46:02'),
('25', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'vxzvxcv', '2018-10-07 11:51:38'),
('26', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'gdsgdsgs ', '2018-10-07 11:52:42'),
('27', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'dsfasdfas', '2018-10-07 12:03:41'),
('28', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'cb85fe68-06b3-11eb-a500-0e4b956b9dd1', NULL, 'dfasdfasdf asdf ', '2018-10-07 12:03:54'),
('29', '79d14338-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'fgsdfg sdfg ', '2018-10-07 12:09:11'),
('30', '79d14338-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'sdfsdf sdf ', '2018-10-07 12:10:30'),
('31', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'sdf sdaf ', '2018-10-07 12:10:55'),
('32', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', '31', 'response :)', '2018-10-07 12:17:53'),
('33', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'Test', '2018-10-07 12:19:20'),
('34', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'cvzxcv zv', '2018-10-07 12:27:35'),
('35', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'dgfsdfg', '2018-10-07 12:51:32'),
('36', '6a75e736-06b1-11eb-a500-0e4b956b9dd1', 'cb85fe68-06b3-11eb-a500-0e4b956b9dd1', NULL, 'dfsd sfd s', '2018-10-07 13:03:39'),
('37', '6a75e736-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', NULL, 'sdfsdfs', '2018-10-07 13:18:02'),
('38', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', '37', 'This is a response!!!', '2018-10-07 13:41:53'),
('39', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', '38', 'asdf asfd asdf ', '2018-10-07 13:55:31'),
('40', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', '39', 'HELLO EVERYONE', '2018-10-07 13:57:13'),
('41', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', '33', 'Hi there :) ', '2018-10-07 14:00:10'),
('42', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1', '41', 'Hi to you too!', '2018-10-07 14:00:27');

-- --------------------------------------------------------

--
-- Table structure for table `dislikes`
--

CREATE TABLE `dislikes` (
  `id` char(36) NOT NULL,
  `userId` char(36) NOT NULL,
  `commentId` char(36) DEFAULT NULL,
  `videoId` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_dislikes_user` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_dislikes_comment` FOREIGN KEY (`commentId`) REFERENCES `comments` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_dislikes_video` FOREIGN KEY (`videoId`) REFERENCES `videos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` char(36) NOT NULL,
  `userId` char(36) NOT NULL,
  `commentId` char(36) DEFAULT NULL,
  `videoId` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_likes_user` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_likes_comment` FOREIGN KEY (`commentId`) REFERENCES `comments` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_likes_video` FOREIGN KEY (`videoId`) REFERENCES `videos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `userId`, `commentId`, `videoId`) VALUES
('22', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', NULL, '46108923-06b3-11eb-a500-0e4b956b9dd1'),
('23', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', NULL, 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1'),
('25', '1935f7b3-0706-11eb-a942-0242ac110002', NULL, 'a1c7565e-06b3-11eb-a500-0e4b956b9dd1'),
('31', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', '42', NULL),
('33', '3b14a9cc-06b1-11eb-a500-0e4b956b9dd1', NULL, '0693c292-06b4-11eb-a500-0e4b956b9dd1');

COMMIT;
