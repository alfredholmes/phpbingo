			var n=1;
			

			function addfield(div)
			{
				var container = document.getElementById("phrase");
				var line = document.createElement('div');
				var input = document.createElement('input');
				var id = div + n
				line.id = id;
				input.type = "text";
				input.name = id;
				
				var remove = document.createElement('a');
				remove.href = '#';
				
				
				
				remove.appendChild(document.createTextNode(" Remove"));
				

				line.appendChild(input);
				line.appendChild(remove);
				container.appendChild(line);
				line.appendChild(document.createElement("br"));
			
				
				remove.addEventListener("click", function() { removefield(id) });
				n++;
			}

			function removefield(id)
			{
				field = document.getElementById(id);
				console.log(id);	
				field.parentNode.removeChild(field);
			}

			function newuser(div)
			{
				var users = [<?php foreach($users as $person) echo "'$person',";?>];
				var user = '<?php echo $user; ?>';
				select = document.createElement("select");
				select.name = 'person' + n;

				for (var i = 0; i < users.length; i++) 
				{
					option = document.createElement('option');
					option.value = users[i];
					option.appendChild(document.createTextNode(users[i]));
					if(users[i] == user)
					{
						option.selected = 'selected';
					}
					select.appendChild(option);		
				};

				var line = document.createElement('div');
				var id = line.id =  div + n;
				console.log(id);

				remove = document.createElement('a');
				remove.href = '#';

				remove.addEventListener("click", function() { removefield(id)});
				remove.appendChild(document.createTextNode('remove'));

				line.appendChild(select);
				line.appendChild(remove);
				line.appendChild(document.createElement('br'));

				document.getElementById(div).appendChild(line);
				
				n++;
					
			}