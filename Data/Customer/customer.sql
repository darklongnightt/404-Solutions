

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";



CREATE TABLE `customer` (
  `USERID` varchar(32) NOT NULL,
  `PASSWORD` varchar(32) NOT NULL,
  `FIRSTNAME` varchar(150) NOT NULL,
  `LASTNAME` varchar(150) NOT NULL,
  `EMAIL` varchar(320) NOT NULL,
  `DOB` date NOT NULL,
  `GENDER` varchar(1) NOT NULL,
  `PHONENO` int(15) NOT NULL
);


INSERT INTO `customer` (`USERID`, `PASSWORD`, `FIRSTNAME`, `LASTNAME`, `EMAIL`, `DOB`, `GENDER`, `PHONENO`) VALUES
('ADMchoiguoh', '66rkruda', 'Stefan', 'Seow', 'stefanseow@email.com', '1949-04-16', 'M', 81300585),
('ADMlimwenl376', 'gptrp8s2', 'Rahmah', 'Othman', 'rahmahothman@email.com', '1947-10-16', 'F', 93693415),
('CUSabdulm483', 'xwdze569', 'Mikhail', 'Ismail', 'mikhailismail@email.com', '1953-07-04', 'M', 90606363),
('CUSaishaee865', 'mcv39njf', 'Aisha', 'Ee', 'aishaee@email.com', '1964-03-15', 'F', 90945796),
('CUSalfredoh', 'hd9j7m69', 'Aqil', 'Hasan', 'aqilhasan@email.com', '1952-08-22', 'M', 91825974),
('CUSalfreds274', 'pzygq4bs', 'Demond', 'Chai', 'demondchai@email.com', '1983-09-04', 'M', 90186277),
('CUSamandal270', 'd7py4uh3', 'Cathy', 'Wong', 'cathywong@email.com', '2003-05-05', 'F', 90618878),
('CUSamandaw859', 'h6f6y5fc', 'Lynn', 'Yeo', 'lynnyeo@email.com', '1976-07-09', 'F', 95554075),
('CUSamyraman', 'pnnx49sy', 'Susie', 'Kwok', 'susiekwok@email.com', '1989-04-07', 'F', 93539547),
('CUSaqilhasan82', 'jkxtsf5y', 'Godfrey', 'Han', 'godfreyhan@email.com', '1994-02-28', 'M', 96932503),
('CUSbenton327', 'xk3uvb5d', 'Jonas', 'Leong', 'jonasleong@email.com', '1967-08-11', 'M', 90764287),
('CUSbernadette59', '6ff72fy4', 'Wee', 'Mun-wei', 'weemun-wei@email.com', '1975-01-15', 'F', 95394010),
('CUSbradleyc', 'w6rd9znv', 'Keaton', 'Lok', 'keatonlok@email.com', '1979-10-29', 'M', 84576641),
('CUScartereng', 'gdpg9pxr', 'Neal', 'Si', 'nealsi@email.com', '2005-11-10', 'M', 97293505),
('CUScathywon91', '469qwmcs', 'Christina', 'Guo', 'christinaguo@email.com', '1953-09-06', 'F', 86675256),
('CUScheahsiew', '8eub9eyu', 'Sandy', 'Siew', 'sandysiew@email.com', '1980-02-17', 'F', 91683179),
('CUSchloeto454', 'zf7md2dg', 'Chloe', 'To', 'chloeto@email.com', '1984-08-18', 'F', 96469841),
('CUSchrist902', 'r6hx2v7g', 'Amanda', 'Wong', 'amandawong@email.com', '1958-05-20', 'F', 84605653),
('CUSchristi959', 'vyxcpd9a', 'Eleanore', 'Iswaran', 'eleanoreiswaran@email.com', '1942-08-02', 'F', 99879161),
('CUSchristina', 'az4mggy7', 'Jamie', 'Chai', 'jamiechai@email.com', '1968-01-22', 'F', 95956950),
('CUSconniefang', 'zkez4rut', 'Connie', 'Fang', 'conniefang@email.com', '1953-12-26', 'F', 96469842),
('CUScorayeoh1', 'p8y9q9a8', 'Christine', 'Lok', 'christinelok@email.com', '1986-01-28', 'F', 96903923),
('CUScorinnel196', 'x4qmph8w', 'Selena', 'Lee', 'selenalee@email.com', '1956-05-31', 'F', 91410284),
('CUSdarianku957', 'e22u9pyg', 'Gavin', 'Neo', 'gavinneo@email.com', '2000-02-14', 'M', 98789164),
('CUSdarlen410', 'ndq9h9mk', 'Dorothy', 'Soh', 'dorothysoh@email.com', '1977-09-12', 'F', 87236205),
('CUSdemondc97', 'jg34n3d2', 'Kyle', 'Chan', 'kylechan@email.com', '2009-11-03', 'M', 99273016),
('CUSdorarajoo', 'eg6hqr86', 'Dora', 'To', 'dorato@email.com', '1979-02-10', 'F', 97535270),
('CUSdorato255', 'mzxvntn8', 'Joey', 'Teo', 'joeyteo@email.com', '1946-11-01', 'F', 96207136),
('CUSdorothys', 'y8jff56a', 'Chloe', 'Han', 'chloehan@email.com', '1979-12-31', 'F', 90137172),
('CUSedward23', 'epqr2pkg', 'Russell', 'Tan', 'russelltan@email.com', '1966-03-06', 'M', 93958364),
('CUSeleanorei1', '2jgh9jta', 'Bernadette', 'Vijiaratnam', 'bernadettevijiaratnam@email.com', '1993-04-16', 'F', 95304341),
('CUSelisakh896', 'f5vc5cp8', 'Elisa', 'Khim', 'elisakhim@email.com', '1977-11-04', 'F', 99971965),
('CUSelmirach', 'fm6dur22', 'Elmira', 'Charteris', 'elmiracharteris@email.com', '1966-11-04', 'F', 93605251),
('CUSemmasi315', 'm2zys56z', 'Cora', 'Yeoh', 'corayeoh@email.com', '1973-01-24', 'F', 95934989),
('CUSgabech924', 'f3p4qee5', 'Lau Wee', 'Kok ', 'lau weekok @email.com', '2002-12-24', 'M', 88741700),
('CUSgarettch9', 'b9vn34uh', 'Mike', 'Ang', 'mikeang@email.com', '1966-12-27', 'M', 93969008),
('CUSgavinneo', 'q3g4d6wv', 'Pete', 'Tan', 'petetan@email.com', '1967-04-15', 'M', 93403647),
('CUSgenela11', 'cs46m7au', 'Stanley', 'Monteiro', 'stanleymonteiro@email.com', '1963-08-01', 'M', 97576843),
('CUSgodfrey77', 's4k4sqvf', 'Edward', 'Iswaran', 'edwardiswaran@email.com', '1983-03-24', 'M', 91311611),
('CUShafsahhas803', 'zbk5crug', 'Hafsah', 'Hasan', 'hafsahhasan@email.com', '1993-07-06', 'F', 92254517),
('CUSjamiecha', 'veugf8a9', 'Darlene', 'Pei', 'darlenepei@email.com', '1953-03-29', 'F', 82460434),
('CUSjamieya348', 'nhkdy6t5', 'Dora', 'Rajoo', 'dorarajoo@email.com', '1987-05-21', 'F', 98251009),
('CUSjoesph201', 'y6xk75gs', 'Lenny', 'Goh', 'lennygoh@email.com', '1946-11-04', 'M', 89749456),
('CUSjoeyteo43', 'bmaqyn8x', 'Anabelle', 'Siok', 'anabellesiok@email.com', '1952-09-28', 'F', 87084131),
('CUSjonasleong67', 'pjav4jws', 'Jonathan', 'Peh', 'jonathanpeh@email.com', '1976-09-20', 'M', 87494952),
('CUSjonathanpe21', '4arrdjhk', 'Alfred', 'Saram', 'alfredsaram@email.com', '1949-06-24', 'M', 97405508),
('CUSjovano898', 'v9kt39a8', 'Wen Loong', 'Lim', 'wen loonglim@email.com', '1954-02-05', 'M', 91818592),
('CUSkadengan3', 'r3egh797', 'Stanton', 'Teoh', 'stantonteoh@email.com', '1970-10-25', 'M', 99263502),
('CUSkeatonl71', 'f4jkes7t', 'Carter', 'Eng', 'cartereng@email.com', '1951-12-07', 'M', 83553163),
('CUSkimpang6', 'bpyw85pr', 'Kim', 'Pang', 'kimpang@email.com', '1987-07-13', 'F', 98681316),
('CUSkohcheng', 'vkxmx2jt', 'Kaden', 'Gan', 'kadengan@email.com', '1963-05-20', 'M', 90094256),
('CUSkohpohgek3', 'j4xn8vg5', 'Poh Gek', 'Soh', 'poh geksoh@email.com', '2001-02-22', 'F', 93755175),
('CUSkylechan845', 'ch4bfse8', 'Abdul', 'Mohamad', 'abdulmohamad@email.com', '1984-03-04', 'M', 81688287),
('CUSlaukok243', '835zdvd4', 'Alfred', 'Oh', 'alfredoh@email.com', '2007-07-09', 'M', 94546353),
('CUSleliakee', 'm277sbye', 'Rachel', 'Li', 'rachelli@email.com', '1986-04-11', 'F', 98392770),
('CUSlengkhor452', 'wkz8g9b2', 'Marion', 'Yu', 'marionyu@email.com', '1948-04-28', 'F', 93107984),
('CUSlennygoh', 'h29xe4ge', 'Stanton', 'Lam', 'stantonlam@email.com', '1946-02-19', 'M', 90782695),
('CUSleonleng', 's64pjdpd', 'Michel', 'Tong', 'micheltong@email.com', '1947-12-11', 'M', 92215966),
('CUSlinavara8', 'qtd97jyc', 'Olivia', 'Seetoh', 'oliviaseetoh@email.com', '1956-05-29', 'F', 97738132),
('CUSlynnye108', 'yudcz39j', 'Jamie', 'Yang', 'jamieyang@email.com', '2007-08-15', 'F', 97354285),
('CUSmadiso871', 'mzrt2ruf', 'Madison', 'Seow', 'madisonseow@email.com', '1953-10-11', 'F', 88040828),
('CUSmarionyu847', 'w25tuhy4', 'Lina', 'Varathan', 'linavarathan@email.com', '2003-12-13', 'F', 80731142),
('CUSmarkusfu4', 'ce8nu96v', 'Stanley', 'Seow', 'stanleyseow@email.com', '1998-02-12', 'M', 81754507),
('CUSmartinfu371', '45p7cc8d', 'Jovan', 'Oh', 'jovanoh@email.com', '1988-11-14', 'M', 98419504),
('CUSmathewf430', 'jv9b9jx2', 'Rowan', 'Foo', 'rowanfoo@email.com', '1949-05-08', 'M', 91781137),
('CUSmelvinmok672', '7b7mza2r', 'Benton', 'Leow', 'bentonleow@email.com', '1992-09-17', 'M', 95149441),
('CUSmikeang286', 'a53mp5ns', 'Bradley', 'Chua', 'bradleychua@email.com', '2000-01-12', 'M', 99230342),
('CUSmikekhoo9', 'zhx79sp3', 'Tony', 'Quek', 'tonyquek@email.com', '1992-08-30', 'M', 96087164),
('CUSmikhail13', 'su2g4v65', 'Gene', 'Lai', 'genelai@email.com', '1995-10-21', 'M', 90989047),
('CUSnabilamo', 'v2s7z656', 'Nabila', 'Mohamad', 'nabilamohamad@email.com', '1962-12-01', 'F', 98858772),
('CUSnadiang563', 'f5k8u5bj', 'Nadia', 'Ng', 'nadiang@email.com', '2009-01-28', 'F', 89680866),
('CUSnealsi435', 'z8bwjknt', 'Mike', 'Khoo', 'mikekhoo@email.com', '1945-04-02', 'M', 94008637),
('CUSneosiewhon4', 'bvzcs2ep', 'Christina', 'Low', 'christinalow@email.com', '1960-02-25', 'F', 97716058),
('CUSnicoleoh', 'jqtsuv45', 'Lelia', 'Kee', 'leliakee@email.com', '1979-04-10', 'F', 84920367),
('CUSolivias6', 'xupef22a', 'Neo', 'Siew', 'neosiew@email.com', '1980-08-25', 'F', 92155722),
('CUSpetetan9', '7sb7w7dm', 'Leon', 'Leng', 'leonleng@email.com', '1953-02-08', 'M', 99673846),
('CUSpoonhanch27', 's4j25fzm', 'Nicole', 'Oh', 'nicoleoh@email.com', '1975-09-29', 'F', 96834457),
('CUSrachelli5', '67cpxd8z', 'Roslyn', 'Tan', 'roslyntan@email.com', '1995-09-25', 'F', 96434845),
('CUSrahmah308', '5da4ufm7', 'Amy', 'Ramanathan', 'amyramanathan@email.com', '1995-03-07', 'F', 85863949),
('CUSroberttan', '5n2h8c68', 'Kok Wee', 'Zhen', 'kok weezhen@email.com', '1951-04-08', 'M', 85319481),
('CUSrowanfoo262', 'pz5rkwvw', 'Cheng Yu', 'Koh', 'cheng yukoh@email.com', '1959-07-24', 'M', 97680580),
('CUSrussel206', 'qfczjn44', 'Robert', 'Tan', 'roberttan@email.com', '1964-07-25', 'M', 99383249),
('CUSselena345', 'tzh5m6j7', 'Emma', 'Si', 'emmasi@email.com', '1972-03-28', 'F', 92500041),
('CUSseowsiewwe6', '7ccgvvz5', 'Yumna', 'Wahid', 'yumnawahid@email.com', '1957-02-15', 'F', 96217326),
('CUSstanleym', 'bzp27p4z', 'Joesph', 'Yeo', 'joesphyeo@email.com', '1993-11-12', 'M', 95722162),
('CUSstanleys608', 'g6paf92v', 'Wilson', 'Teoh', 'wilsonteoh@email.com', '1940-05-05', 'M', 94250984),
('CUSstantan127', 'cy9xm3b8', 'Melvin', 'Mok', 'melvinmok@email.com', '1987-03-08', 'M', 98067399),
('CUSstanto809', 'w58rjnem', 'Garett', 'Choo', 'garettchoo@email.com', '1941-06-20', 'M', 90792437),
('CUSstantonl315', '34ujff6z', 'Mathew', 'Fong', 'mathewfong@email.com', '1972-08-09', 'M', 94822577),
('CUSstefan461', 'dndc2a53', 'Gabe', 'Chang', 'gabechang@email.com', '1994-08-19', 'M', 80069270),
('CUSsusiekwok9', '9sukn65p', 'Leng', 'Khor', 'lengkhor@email.com', '1958-01-07', 'F', 88572251),
('CUStanmeilin6', 'cnv8spy2', 'Tiffany', 'Choi', 'tiffanychoi@email.com', '1949-08-18', 'F', 96509532),
('CUSterry94', '68rkrudb', 'Terry', 'Toh', 'terrytoh@email.com', '1994-08-10', 'M', 97968912),
('CUStiffanycho', '3uwurmwx', 'Corinne', 'Lee', 'corinnelee@email.com', '1955-07-25', 'F', 94171156),
('CUStongwenj256', 'bcq9k56p', 'Stan', 'Tan', 'stantan@email.com', '1941-11-12', 'M', 99871484),
('CUStonyqu90', 'kuc3p2nw', 'Markus', 'Fu', 'markusfu@email.com', '2009-08-04', 'M', 90148289),
('CUSweemun-60', '47u4ya6u', 'Elisa', 'Tan', 'elisatan@email.com', '2011-01-29', 'F', 90429649),
('CUSwilsont337', 'wvqs8dfy', 'Martin', 'Fu', 'martinfu@email.com', '2011-09-15', 'M', 98441579),
('CUSyangsiokh', 'zm4zca5m', 'Seow', 'Siew', 'seowsiew@email.com', '2000-02-16', 'F', 86240667),
('CUSyumnaw354', '87wq538k', 'Amanda', 'Lee', 'amandalee@email.com', '1972-05-03', 'F', 85420757),
('CUSzhenkokw429', 'u5747kb8', 'Darian', 'Kumar', 'dariankumar@email.com', '1987-09-02', 'M', 96503194);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`USERID`);
COMMIT;

