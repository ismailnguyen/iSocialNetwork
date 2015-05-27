-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 27 Mai 2015 à 15:00
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `isocialnetwork`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `idComment` int(11) NOT NULL AUTO_INCREMENT,
  `user_idUser` int(11) NOT NULL,
  `post_idPost` int(11) NOT NULL,
  `content` text NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`idComment`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `comment`
--

INSERT INTO `comment` (`idComment`, `user_idUser`, `post_idPost`, `content`, `createdDate`) VALUES
(1, 4, 1, 'Merci de ne pas me r&eacute;pondre -_-&quot;', '2015-05-19 22:55:05'),
(2, 4, 1, 'Ouh ouh personne ?', '2015-05-19 23:08:11'),
(3, 1, 1, 'La flemme de r&eacute;pondre !', '2015-05-19 23:09:57');

-- --------------------------------------------------------

--
-- Structure de la table `comment_like`
--

CREATE TABLE IF NOT EXISTS `comment_like` (
  `idComment_like` int(11) NOT NULL AUTO_INCREMENT,
  `user_idUser` int(11) NOT NULL,
  `comment_idComment` int(11) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`idComment_like`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `comment_tag`
--

CREATE TABLE IF NOT EXISTS `comment_tag` (
  `idComment_tag` int(11) NOT NULL AUTO_INCREMENT,
  `user_idUser` int(11) NOT NULL,
  `user_idFriend` int(11) NOT NULL,
  `comment_idComment` int(11) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`idComment_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `friendship`
--

CREATE TABLE IF NOT EXISTS `friendship` (
  `idFriendship` int(11) NOT NULL AUTO_INCREMENT,
  `user_idUser` int(11) NOT NULL,
  `user_idFriend` int(11) NOT NULL,
  `state` tinyint(3) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`idFriendship`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `idNotification` int(11) NOT NULL AUTO_INCREMENT,
  `user_idUser` int(11) NOT NULL,
  `content` text NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`idNotification`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `idPost` int(11) NOT NULL AUTO_INCREMENT,
  `user_idUser` int(11) NOT NULL,
  `content` text NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`idPost`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `post`
--

INSERT INTO `post` (`idPost`, `user_idUser`, `content`, `createdDate`) VALUES
(1, 4, 'Hey les gars qui sait quand est la soutenance de Web ???', '2015-05-19 22:50:15'),
(2, 4, 'Moi en bikini a Hawaii !', '2015-05-24 18:20:20'),
(3, 7, 'Contenu de la publication modifi&eacute;e', '2015-05-27 14:25:47');

-- --------------------------------------------------------

--
-- Structure de la table `post_like`
--

CREATE TABLE IF NOT EXISTS `post_like` (
  `idPost_like` int(11) NOT NULL AUTO_INCREMENT,
  `user_idUser` int(11) NOT NULL,
  `post_idPost` int(11) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`idPost_like`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `post_tag`
--

CREATE TABLE IF NOT EXISTS `post_tag` (
  `idPost_tag` int(11) NOT NULL AUTO_INCREMENT,
  `user_idUser` int(11) NOT NULL,
  `user_idFriend` int(11) NOT NULL,
  `post_idPost` int(11) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`idPost_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(400) NOT NULL,
  `gender` int(11) NOT NULL,
  `birthdate` date NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`idUser`, `firstname`, `lastname`, `email`, `password`, `gender`, `birthdate`, `createdDate`) VALUES
(1, 'Ismail', 'NGUYEN', 'nguyen.ismail@gmail.com', '111237f0d8fea2cc289f1a605fa3753bb476b1f45ec2866a80f9ba77a5b447af', 1, '1992-08-30', '2015-05-18 00:17:42'),
(2, 'Bruno', 'VACQUEREL', 'vacquerel.bruno@gmail.com', '6545f4d13c6b954904e2cededff534b5f50260c337dfa', 1, '1993-03-04', '2015-05-18 00:23:08'),
(4, 'Fabien', 'GAMELIN', 'fabien.gamelin@gmail.com', '111237f0d8fea2cc289f1a605fa3753bb476b1f45ec2866a80f9ba77a5b447af', 1, '1993-04-28', '2015-05-19 22:22:10'),
(5, 'Emmanuel', 'Peter', 'e.peter@esgi.fr', '88dba909799406cfaec867370cf78da87d482beaff9da95c7dd49494ea2a7644', 1, '1983-05-06', '2015-05-27 11:25:51'),
(6, 'Emmanuel', 'Peter', 'e.peter2@esgi.fr', '88dba909799406cfaec867370cf78da87d482beaff9da95c7dd49494ea2a7644', 1, '1983-05-06', '2015-05-27 11:26:14'),
(7, 'Georges', 'Lucas', 'e.peter@myges.fr', '88dba909799406cfaec867370cf78da87d482beaff9da95c7dd49494ea2a7644', 1, '1983-05-06', '2015-05-27 11:27:52');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
