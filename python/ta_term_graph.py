# -*- coding: utf-8 -*-
"""TA Term Graph.ipynb

Automatically generated by Colaboratory.

Original file is located at
    https://colab.research.google.com/drive/1Dcq7ho-SrzHOgH-6w6AiJwwwmbHsGJ6U

TA TERM GRAPH

Proses Pembuatan Tabel 1 yang berisi metadata
"""

from tabulate import tabulate
title=[ 'Article-ID', 'Terms in Title and Keywords', 'Terms Found in Abstracts','Publication Year','Authors','References']
table = [  
           [ "a1", ['a','b','c'],   ['a','b','c','k','l']    ,'1993',['p1','p2']                                              ]
         , [ "a2", ['c','d','e'],   ['a','c','d','e','m','n'],'1993',['p1','p3']                                              ]
         , [ "a3", ['f','g','h'],   ['c','d','f','g','h','o'],'1993',['p2','p4','p5']                                         ]
         , [ "a4", ['i','j'],       ['c','d','p','q']        ,'1994',['p3','p6']      ,['a1','a2']                            ]
         , [ "a5", ['dj','dk'],     ['a','dj','dk','m','r']  ,'1994',['p1','p7']      ,['a1','a2','a3']                       ]
         , [ "a6", ['d','ac','ad'], ['d','ac','ad','s','t']  ,'1994',['p8','p9']      ,['a1','a3']                            ]
         ]
print("TABLE I. EXAMPLE OF METADATA INFORMATION ON A CLUSTER OF ARTICLES")
print(title)
print(tabulate(table))

"""Proses mendapatkan pasangan (Artikel ID & Author & Reference) dan mendapatkan list penulis yang ada"""

pairs=[]
authors=[]
for i in table:
  row=[]
  # i[0]= "Article-ID" ,  i[4]= "Authors"
  row.append(i[0])
  row.append(i[4])
  for penulis in i[4]:
    # authors = ['p1', 'p2','p1', 'p3', .....]
    authors.append(penulis)
  try:
    # i[5]= "References"
    row.append(i[5])
  except:
    print("")
  # pairs berisi pasangan Artikel ID & Author & Reference"
  pairs.append(row)
print("Artikel ID & Author & Reference")
for i in pairs:
  print(i)
  print("\n")
# authors berisi data penulis namus banyak duplikasi maka disortir agar duplikasinya dapat dihilangkan 
authors=sorted(set(authors))
print("Daftar Author")
for y in authors:
  print(y)
  print("\n")

print("Semua Kemungkinan Relasi Antar Penulis Yang Dapat Terjadi")
author_matrix=[]
for author_x in authors:
  for author_y in authors:
    row=[]
    row.append(author_x)
    row.append(author_y)
    author_matrix.append(row)
for x in author_matrix:
  print(x)

"""Proses Perhitungan Degree of Interest"""

for i in pairs:
  try:
    penulisList=i[1]
    authorList=i[2]
    authorListExpand=[]
    print(penulisList+authorList)
    for author in authorList:
      row_author=pairs[index_2d(pairs, author)][1]
      print(row_author)
      for every_author in row_author:
        print(every_author)
        authorListExpand.append(every_author)
    print("\n")
    for authorListExpandChild in authorListExpand:
      print("child:")
      print(authorListExpandChild)
    print("\n")

    for penulis in penulisList:
      for child in authorListExpand:
        if penulis is child:
          continue
        print("penulis:",penulis,child)
        try:
          index=author_matrix.index([penulis, child])
          author_matrix[index].append(authorListExpand.count(child))
          print("nilai:",author_matrix[index][2])
          # try:
          #   author_matrix[index][2]=author_matrix[index][2]+1
          #   print("coba:",author_matrix[index][2])
          # except:
          #   author_matrix[index].append(1)
          #   print("nilai:",author_matrix[index][2])
          
        except:
            continue
    
  except:
    continue

def index_2d(myList, v):
    for i, x in enumerate(myList):
        if v in x:
            return i #, x.index(v)

"""Data Hasil Perhitungan Degree of Interest (blm dikasih tabel)"""

#Table 2
for x in author_matrix:
  print(x)

import pandas as pd
pretable2=[]
for x in authors:
  authortmp=[]
  for y in author_matrix:
    if y[1] in x:
      try:
        authortmp.append(y[2])
      except:
        authortmp.append(0)
  pretable2.append(authortmp)
# print(pretable2)
table2=pd.DataFrame(pretable2, columns=authors,index=authors)
print("TABLE II. AUTHOR ADJACENT MATRIX OF 1994 PUBLICATION")
print(table2)

"""Proses Pembuatan Term Graph menggunakan Tabel 2 menggunakan networkx"""

import numpy as np
import matplotlib.pyplot as plt
import networkx as nx
# rows, cols = np.where(table2 >= 1)
# edges = zip(rows.tolist(), cols.tolist())
# gr = nx.Graph()
# gr.add_edges_from(edges)
# nx.draw(gr, node_size=500,with_labels=True)
# plt.show()


G = nx.Graph()
rows1, cols1 = np.where(table2 == 1)
edges1 = zip(rows1.tolist(), cols1.tolist())
for x,y in edges1:
  G.add_edge(x, y, weight=1)
rows2, cols2 = np.where(table2 == 2)
edges2 = zip(rows2.tolist(), cols2.tolist())
for x,y in edges2:
  G.add_edge(x, y, weight=2)

elarge = [(u, v) for (u, v, d) in G.edges(data=True) if d["weight"] ==1 ]
esmall = [(u, v) for (u, v, d) in G.edges(data=True) if d["weight"] == 2]

pos = nx.spring_layout(G, seed=7)  # positions for all nodes - seed for reproducibility

# nodes
nx.draw_networkx_nodes(G, pos, node_size=700)

# edges
nx.draw_networkx_edges(G, pos, edgelist=elarge, width=6)
nx.draw_networkx_edges(
    G, pos, edgelist=esmall, width=6, alpha=0.5, edge_color="b", style="dashed"
)

# node labels
nx.draw_networkx_labels(G, pos, font_size=20, font_family="sans-serif")
# edge weight labels
edge_labels = nx.get_edge_attributes(G, "weight")
nx.draw_networkx_edge_labels(G, pos, edge_labels)

ax = plt.gca()
ax.margins(0.08)
plt.axis("off")
plt.tight_layout()
print("term Graph")
plt.show()



# G = nx.from_numpy_matrix(np.matrix(table2), create_using=nx.DiGraph)
# layout = nx.spring_layout(G)
# nx.draw(G, layout)
# nx.draw_networkx_edge_labels(G, pos=layout)
# plt.show()

"""Mengubal Tabel 2 menjadi Tabel 3, dimana tabel 3 sama seperti tabel 2 hanya perbedaannya ada penjumlah per row & col di Tabelnya,selebihnya sama"""

sumrow=[]
sumcol=[]
lenauthor=len(authors)
for x in range(lenauthor):
  nilai=0
  for y in range(lenauthor):
    nilai=nilai+pretable2[x][y]
  sumrow.append(nilai)
print("p1p9")
print(sumrow)

sumcol=[]
for x in range(lenauthor):
  nilai=0
  for y in range(lenauthor):
    nilai=nilai+pretable2[y][x]
  sumcol.append(nilai)
sumcol.append(0)
print("p9p1")
print(sumcol)
for x in range(lenauthor):
  pretable2[x].append(sumrow[x])
pretable2.append(sumcol)
print(pretable2)
print("tabel 3: Add Total Row & Col")
table2=pd.DataFrame(pretable2)
print(table2)

"""![rumus 1.png](https://i.ibb.co/bKKGWfv/rumus-1.png)
Membuat tabel baru menggunakan perhitungan rumus diatas
"""

pretable3=pretable2
for x in range(lenauthor):
  for y in range(lenauthor):
    if pretable3[lenauthor][y] == 0:
      # print("nilaiku="+str(pretable3[x][y]))
      pretable3[x][y]=1/lenauthor
    else:
      # print("nilaiku="+str(pretable3[x][y]))
      pretable3[x][y]=pretable3[x][y]/pretable3[lenauthor][y]
table3=pd.DataFrame(pretable3)
print("tabel 3:new adj Matrix")
print(table3)

"""![rumus 2.png](https://i.ibb.co/wp6Gyc3/rumus-2.png)
Membuat perhitungan akhir menggunakan perhitungan rumus diatas
"""

d=0.850466963
table4=[]
row=[]
for x in range(lenauthor):
  row.append(1/lenauthor)
table4.append(row)
for y in range(100):
   rowbaru=[]
   for x in range(lenauthor): 
     nilai=(1-d)+d*np.matmul(pretable3[x][0:lenauthor],row[0:lenauthor])
     rowbaru.append(nilai)
   table4.append(rowbaru)
   selisih=abs(np.array(row)-np.array(rowbaru))
   ns=max(selisih)
   if ns < 0.001:break;
   #print(ns)
   row=rowbaru
rank=[sorted(row,reverse=True).index(x) for x in row]
table4.append(rank)   

table5=pd.DataFrame(table4)
print("tabel 3: Ranking")
print(table5.T)