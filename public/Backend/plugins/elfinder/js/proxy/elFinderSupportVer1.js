"use strict";
/**
 * elFinder transport to support old protocol.
 *
 * @example
 * $('selector').elfinder({
 *   .... 
 *   transport : new elFinderSupportVer1()
 * })
 *
 * @author Dmitry (dio) Levashov
 **/
window.elFinderSupportVer1 = function(upload) {
	var self = this;
	
	this.upload = upload || 'auto';
	
	this.init = function(fm) {
		this.fm = fm;
		this.fm.parseUploadData = function(text) {
			var data;

			if (!$.trim(text)) {
				return {error : ['errResponse', 'errDataEmpty']};
			}

			try {
				data = $.parseJSON(text);
			} catch (e) {
				return {error : ['errResponse', 'errDataNotJSON']}
			}
			
			return self.normalize('upload', data);
		}
	}
	
	
	this.send = function(opts) {
		var self = this,
			fm = this.fm,
			dfrd = $.Deferred(),
			cmd = opts.data.cmd,
			args = [],
			_opts = {},
			data,
			xhr;
			
		dfrd.abort = function() {
			xhr.state() == 'pending' && xhr.abort();
		}
		
		switch (cmd) {
			case 'open':
				opts.data.tree = 1;
				break;
			case 'parents':
			case 'tree':
				return dfrd.resolve({tree : []});
				break;
			case 'get':
				opts.data.cmd = 'read';
				opts.data.current = fm.file(opts.data.target).phash;
				break;
			case 'put':
				opts.data.cmd = 'edit';
				opts.data.current = fm.file(opts.data.target).phash;
				break;
			case 'archive':
			case 'rm':
				opts.data.current = fm.file(opts.data.targets[0]).phash;
				break;
			case 'extract':
			case 'rename':
			case 'resize':
				opts.data.current = fm.file(opts.data.target).phash;
				break;
			case 'duplicate':
				_opts = $.extend(true, {}, opts);

				$.each(opts.data.targets, function(i, hash) {
					$.ajax($.extend(_opts, {data : {cmd : 'duplicate', target : hash, current : fm.file(hash).phash}}))
						.error(function(error) {
							fm.error(fm.res('error', 'connect'));
						})
						.done(function(data) {
							data = self.normalize('duplicate', data);
							if (data.error) {
								fm.error(data.error);
							} else if (data.added) {
								fm.trigger('add', {added : data.added});
							}
						})
				});
				return dfrd.resolve({})
				break;
				
			case 'mkdir':
			case 'mkfile':
				opts.data.current = opts.data.target;
				break;
			case 'paste':
				opts.data.current = opts.data.dst
				break;
				
			case 'size':
				return dfrd.resolve({error : fm.res('error', 'cmdsupport')});
				break;
			case 'search':
				return dfrd.resolve({error : fm.res('error', 'cmdsupport')});
				break;
				
		}
		// cmd = opts.data.cmd
		
		xhr = $.ajax(opts)
			.fail(function(error) {
				dfrd.reject(error)
			})
			.done(function(raw) {
				data = self.normalize(cmd, raw);
				
				// cmd != 'open' && self.fm.log(data);
				
				if (cmd == 'paste' && !data.error) {
					fm.sync();
					dfrd.resolve({});
				} else {
					dfrd.resolve(data);
				}
			})
			
		return dfrd;
		
		return $.ajax(opts);
	}
	
	// fix old connectors errors messages as possible
	// this.errors = {
	// 	'Unknown command'                                  : 'Unknown command.',
	// 	'Invalid backend configuration'                    : 'Invalid backend configuration.',
	// 	'Access denied'                                    : 'Access denied.',
	// 	'PHP JSON module not installed'                    : 'PHP JSON module not installed.',
	// 	'File not found'                                   : 'File not found.',
	// 	'Invalid name'                                     : 'Invalid file name.',
	// 	'File or folder with the same name already exists' : 'File named "$1" already exists in this location.',
	// 	'Not allowed file type'                            : 'Not allowed file type.',
	// 	'File exceeds the maximum allowed filesize'        : 'File exceeds maximum allowed size.',
	// 	'Unable to copy into itself'                       : 'Unable to copy "$1" into itself.',
	// 	'Unable to create archive'                         : 'Unable to create archive.',
	// 	'Unable to extract files from archive'             : 'Unable to extract files from "$1".'
	// }
	
	this.normalize = function(cmd, data) {
		var self = this,
			files = {}, 
			filter = function(file) { return file && file.hash && file.name && file.mime ? file : null; },
			phash;

		if ((cmd == 'tmb' || cmd == 'get')) {
			return data;
		}
		
		// if (data.error) {
		// 	$.each(data.error, function(i, msg) {
		// 		if (self.errors[msg]) {
		// 			data.error[i] = self.errors[msg];
		// 		}
		// 	});
		// }
		
		if (cmd == 'upload' && data.error && data.cwd) {
			data.warning = $.extend({}, data.error);
			data.error = false;
		}
		
		
		if (data.error) {
			return data;
		}
		
		if (cmd == 'put') {

			phash = this.fm.file(data.target.hash).phash;
			return {changed : [this.normalizeFile(data.target, phash)]};
		}
		
		phash = data.cwd.hash;
		
		if (data.tree) {
			$.each(this.normalizeTree(data.tree), function(i, file) {
				files[file.hash] = file;
			});
		}
		
		$.each(data.cdc||[], function(i, file) {
			var hash = file.hash;

			if (files[hash]) {
				files[hash].date   = file.date;
				files[hash].locked = file.hash == phash ? true : file.rm === void(0) ? false : !file.rm;
			} else {
				files[hash] = self.normalizeFile(file, phash, data.tmb);
			}
		});
		
		if (!data.tree) {
			$.each(this.fm.files(), function(hash, file) {
				if (!files[hash] && file.phash != phash && file.mime == 'directory') {
					files[hash] = file;
				}
			});
		}
		
		if (cmd == 'open') {
			return {
					cwd     : files[phash] || this.normalizeFile(data.cwd),
					files   : $.map(files, function(f) { return f }),
					options : self.normalizeOptions(data),
					init    : !!data.params,
					debug   : data.debug
				};
		}
		
		
		
		return $.extend({
			current : data.cwd.hash,
			error   : data.error,
			warning : data.warning,
			options : {tmb : !!data.tmb}
		}, this.fm.diff($.map(files, filter)));
		
	}
	
	/**
	 * Convert old api tree into plain array of dirs
	 *
	 * @param  Object  root dir
	 * @return Array
	 */
	this.normalizeTree = function(root) {
		var self     = this,
			result   = [],
			traverse = function(dirs, phash) {
				var i, dir;
				
				for (i = 0; i < dirs.length; i++) {
					dir = dirs[i];
					result.push(self.normalizeFile(dir, phash))
					dir.dirs.length && traverse(dir.dirs, dir.hash);
				}
			};

		traverse([root]);

		return result;
	}
	
	/**
	 * Convert file info from old api format into new one
	 *
	 * @param  Object  file
	 * @param  String  parent dir hash
	 * @return Object
	 */
	this.normalizeFile = function(file, phash, tmb) {
		var mime = file.mime || 'directory',
			size = mime == 'directory' && !file.linkTo ? 0 : file.size,
			info = {
				url    : file.url,
				hash   : file.hash,
				phash  : phash,
				name   : file.name,
				mime   : mime,
				date   : file.date || 'unknown',
				size   : size,
				read   : file.read,
				write  : file.write,
				locked : !phash ? true : file.rm === void(0) ? false : !file.rm
			};
		
		if (file.mime == 'application/x-empty') {
			info.mime = 'text/plain';
		}
		if (file.linkTo) {
			info.alias = file.linkTo;
		}

		if (file.linkTo) {
			info.linkTo = file.linkTo;
		}
		
		if (file.tmb) {
			info.tmb = file.tmb;
		} else if (info.mime.indexOf('image/') === 0 && tmb) {
			info.tmb = 1;
			
		}

		if (file.dirs && file.dirs.length) {
			info.dirs = true;
		}
		if (file.dim) {
			info.dim = file.dim;
		}
		if (file.resize) {
			info.resize = file.resize;
		}
		return info;
	}
	
	this.normalizeOptions = function(data) {
		var opts = {
				path          : data.cwd.rel,
				disabled      : data.disabled || [],
				tmb           : !!data.tmb,
				copyOverwrite : true
			};
		
		if (data.params) {
			opts.api      = 1;
			opts.url      = data.params.url;
			opts.archivers = {
				create  : data.params.archives || [],
				extract : data.params.extract || []
			}
		}
		
		if (opts.path.indexOf('/') !== -1) {
			opts.separator = '/';
		} else if (opts.path.indexOf('\\') !== -1) {
			opts.separator = '\\';
		}
		return opts;
	}
	
	
}
