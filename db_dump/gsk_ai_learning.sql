-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2026 at 01:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gsk_ai_learning`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_history`
--

CREATE TABLE `chat_history` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `provider` varchar(50) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `time_taken` decimal(10,2) DEFAULT NULL,
  `conversation_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_history`
--

INSERT INTO `chat_history` (`id`, `question`, `answer`, `created_at`, `provider`, `model`, `time_taken`, `conversation_id`) VALUES
(7, 'hi, how are you?', 'Hello! I\'m doing well, thanks. How can I help you today?', '2026-06-08 17:18:35', 'ollama', 'qwen2.5:7b', 25.11, 4),
(8, 'I am going to build My first AI Project, I need full assistance from your side, not only for creating the application but also for testing and deploying it, thank you in advance.', 'Sure, what specific type of AI project are you planning?', '2026-06-08 17:20:27', 'ollama', 'qwen2.5:7b', 5.68, 5),
(9, 'My first AI chatbot using PHP, MySQL (Laravel), HTML, CSS, JavaScript.', 'Great! Where will the chatbot interact? Website or standalone application?', '2026-06-08 17:21:39', 'ollama', 'qwen2.5:7b', 8.38, 5),
(10, 'Initially I will built it like a standalone application, lated I will look into it if I can add it somewhere, is that possible?', 'Yes, a standalone app can be integrated later. Proceed with your initial plan.', '2026-06-08 17:22:45', 'ollama', 'qwen2.5:7b', 8.48, 5),
(11, 'who is the owner of openAI?', 'Who owns OpenAI?', '2026-06-08 17:25:51', 'ollama', 'qwen2.5:7b', 3.39, 6),
(12, 'ok, lets start', 'Perfect! Let\'s begin with planning your project.', '2026-06-08 21:43:10', 'ollama', 'qwen2.5:7b', 38.90, 5),
(13, 'Hello Mr Gsk', 'Can you provide more context or ask a specific question?', '2026-06-08 21:58:10', 'ollama', 'qwen2.5:7b', 30.83, 7),
(14, 'chatgpt', 'Who owns ChatGPT?', '2026-06-08 22:04:43', 'ollama', 'qwen2.5:7b', 29.94, 6),
(15, '111', 'Success msg from gemini sample', '2026-06-08 22:06:13', 'gemini sample', 'gemini-2.5-flash', 0.00, 6),
(16, '222', 'Success msg from gemini sample', '2026-06-08 22:10:28', 'gemini sample', 'gemini-2.5-flash', 0.00, 6),
(17, 'Don', 'Success msg from gemini sample', '2026-06-08 22:10:41', 'gemini sample', 'gemini-2.5-flash', 0.00, 8),
(18, 'I am don', 'Success msg from gemini sample', '2026-06-08 22:19:46', 'gemini sample', 'gemini-2.5-flash', 0.00, 8),
(19, 'hi', 'Success msg from gemini sample', '2026-06-08 22:21:34', 'gemini sample', 'gemini-2.5-flash', 0.00, 7),
(20, 'I am good', 'Success msg from gemini sample', '2026-06-08 22:21:43', 'gemini sample', 'gemini-2.5-flash', 0.00, 4);

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `title`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, '999', '2026-06-08 17:18:00', '2026-06-08 23:20:44', '2026-06-09 01:20:44'),
(5, 'I am going to build My first AI Project, I need fu', '2026-06-08 17:18:50', '2026-06-08 21:43:10', NULL),
(6, 'who is the owner of openAI?', '2026-06-08 17:23:52', '0000-00-00 00:00:00', NULL),
(7, 'The boSS!', '2026-06-08 21:57:25', '2026-06-08 22:42:06', NULL),
(8, '333', '2026-06-08 22:10:36', '2026-06-08 23:20:38', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_history`
--
ALTER TABLE `chat_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_history`
--
ALTER TABLE `chat_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
