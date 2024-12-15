<?php
namespace myStagedoor\Request;

enum Type {
	case BASIC;
	case REFERENCE;
	case COALESCE;
	case QUERY;
	case SORT;
	case SEARCH;
	case TASK;
	case INFO;
	case LOGIN;
	case SIGNUP;
	case AUTH;
	case IMAGE;
	case FILE;
	case FOLDER;
	case SPECIAL;

}